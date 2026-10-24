
# Stage 1: Build the Vue application
FROM node:22-alpine AS builder

WORKDIR /app

# Install dependencies first for better build caching
COPY package*.json ./
RUN npm ci

# Copy source files and build production assets
COPY . .

# Vite embeds this value into the frontend at build time
ARG VITE_API_BASE_URL=http://localhost:8000/api
ENV VITE_API_BASE_URL=${VITE_API_BASE_URL}

RUN npm run build


# Stage 2: Serve static files with Nginx
FROM nginx:1.30.5-alpine AS production

# Replace the default Nginx configuration
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copy only the compiled frontend from the builder stage
COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
