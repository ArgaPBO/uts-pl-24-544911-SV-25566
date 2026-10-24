
<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()

const books = ref([])
const loading = ref(false)
const saving = ref(false)
const error = ref('')
const message = ref('')
const editing = ref(false)

const form = ref({
  id: null,
  title: '',
  author: '',
  publish_year: new Date().getFullYear(),
})

function resetForm() {
  form.value = {
    id: null,
    title: '',
    author: '',
    publish_year: new Date().getFullYear(),
  }

  editing.value = false
}

function handleError(err) {
  if (err.response?.status === 401) {
    localStorage.removeItem('token')
    router.replace({ name: 'login' })
    return
  }

  const validationErrors = err.response?.data?.errors

  error.value = validationErrors
    ? Object.values(validationErrors).flat().join(' ')
    : err.response?.data?.message || 'Something went wrong.'
}

async function loadBooks() {
  error.value = ''
  loading.value = true

  try {
    const response = await api.get('/books')
    books.value = response.data.data
  } catch (err) {
    handleError(err)
  } finally {
    loading.value = false
  }
}

function editBook(book) {
  error.value = ''
  message.value = ''

  form.value = {
    id: book.id,
    title: book.title,
    author: book.author,
    publish_year: book.publish_year,
  }

  editing.value = true

  window.scrollTo({ top: 0, behavior: 'smooth' })
}

async function saveBook() {
  error.value = ''
  message.value = ''
  saving.value = true

  try {
    const payload = {
      title: form.value.title,
      author: form.value.author,
      publish_year: Number(form.value.publish_year),
    }

    if (editing.value) {
      await api.put(`/books/${form.value.id}`, payload)
      message.value = 'Book updated successfully.'
    } else {
      await api.post('/books', payload)
      message.value = 'Book created successfully.'
    }

    resetForm()
    await loadBooks()
  } catch (err) {
    handleError(err)
  } finally {
    saving.value = false
  }
}

async function deleteBook(book) {
  if (!window.confirm(`Are you sure you want to delete "${book.title}"?`)) {
    return
  }

  error.value = ''
  message.value = ''

  try {
    await api.delete(`/books/${book.id}`)
    message.value = 'Book deleted successfully.'
    await loadBooks()
  } catch (err) {
    handleError(err)
  }
}

function logout() {
  localStorage.removeItem('token')
  resetForm()
  router.replace({ name: 'login' })
}

onMounted(loadBooks)
</script>

<template>
  <div class="container py-4 py-md-5">
    <header
      class="d-flex flex-column flex-sm-row justify-content-between
             align-items-sm-center gap-3 mb-4"
    >
      <div>
        <h1 class="h2 fw-bold mb-1">Book Catalog</h1>
        <p class="text-secondary mb-0">
          Manage your library collection
        </p>
      </div>

      <button class="btn btn-outline-danger" @click="logout">
        Sign out
      </button>
    </header>

    <div v-if="error" class="alert alert-danger" role="alert">
      {{ error }}
    </div>

    <div v-if="message" class="alert alert-success" role="status">
      {{ message }}
    </div>

    <section class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-white py-3">
        <h2 class="h5 mb-0">
          {{ editing ? 'Edit book' : 'Add a new book' }}
        </h2>
      </div>

      <div class="card-body p-4">
        <form @submit.prevent="saveBook">
          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label for="title" class="form-label">Title</label>
              <input
                id="title"
                v-model.trim="form.title"
                class="form-control"
                maxlength="255"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <label for="author" class="form-label">Author</label>
              <input
                id="author"
                v-model.trim="form.author"
                class="form-control"
                maxlength="255"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <label for="year" class="form-label">
                Publication year
              </label>
              <input
                id="year"
                v-model.number="form.publish_year"
                type="number"
                min="0"
                max="9999"
                step="1"
                class="form-control"
                required
              />
            </div>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="saving"
            >
              {{ saving ? 'Saving...' : editing ? 'Save changes' : 'Add book' }}
            </button>

            <button
              v-if="editing"
              type="button"
              class="btn btn-outline-secondary"
              @click="resetForm"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </section>

    <section class="card shadow-sm border-0">
      <div
        class="card-header bg-white py-3 d-flex
               justify-content-between align-items-center"
      >
        <h2 class="h5 mb-0">Books</h2>

        <button
          class="btn btn-sm btn-outline-primary"
          :disabled="loading"
          @click="loadBooks"
        >
          Refresh
        </button>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <div v-else-if="books.length === 0" class="text-center py-5">
        <p class="text-secondary mb-0">
          No books found. Add your first book above.
        </p>
      </div>

      <div v-else class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Title</th>
              <th scope="col">Author</th>
              <th scope="col">Year</th>
              <th scope="col" class="text-end">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="book in books" :key="book.id">
              <td>{{ book.id }}</td>
              <td class="fw-semibold">{{ book.title }}</td>
              <td>{{ book.author }}</td>
              <td>{{ book.publish_year }}</td>
              <td class="text-end text-nowrap">
                <button
                  class="btn btn-sm btn-outline-primary me-2"
                  :disabled="saving"
                  @click="editBook(book)"
                >
                  Edit
                </button>

                <button
                  class="btn btn-sm btn-outline-danger"
                  :disabled="saving"
                  @click="deleteBook(book)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>
