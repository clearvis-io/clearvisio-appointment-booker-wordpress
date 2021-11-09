# Wordpress plugin for Clearvis.io Appointment Booker

Clearvis.io is a cloud based Practice Management Software (PMS) for optical retailers. It provides complete solution for the daily front- and backoffice tasks in an optical retail store. Clearvis.io includes EHR, POS and CRM features and it is suitable for private practices and retail chains as well.

# Development environment

Docker and docker-compose is required for developing this plugin. You'll also need a clearvis.io subscription with an API key. To start the development environment:

```bash
API_URL=https://clearvis.io/<your-instance-name>/apiV2 API_KEY=<your-api-key> API_STORE_CODE=<your-store-code> docker-compose up -d
```

The above command will start a wordpress instance, which is available on http://localhost:8000/. After configuring wordpress you must enable the Clearvis.io appointment booker plugin. Any change the source code will be effective immediately in the container.
