# Use the official nginx image as the base
FROM nginx:latest

# Install nano text editor
RUN apt-get update && \
    apt-get install -y nano && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

ADD ./nginx.conf /etc/nginx/conf.d/default.conf
WORKDIR /var/www/html

# Expose the port nginx is listening on
EXPOSE 80