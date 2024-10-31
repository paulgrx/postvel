# Postvel

Postvel is a self-hosted solution that provides a personal email server environment designed to send emails from a custom domain.

- REST API for creating, managing, and monitoring email messages and recipients
- Domain-validated email sending based od DKIM and SPF
- Fluentd integration for collect messages statuses
- Docker environment

### Initial Setup

1. **Generate DKIM Keys:**
    - Use the following commands to generate DKIM keys:
      ```bash
      opendkim-genkey -t -s smtp -d yourdomain.com
      ```
    - This will generate two files: `smtp.private` (your private key) and `smtp.txt` (contains the public key).

2. **Add DKIM Public Key to DNS:**
    - Open the `smtp.txt` file and copy the content.
    - Add a new DNS TXT record to your domain with:
        - **Name:** `smtp._domainkey.yourdomain.com`
        - **Type:** TXT
        - **Value:** Paste the public key content from `smtp.txt`.

3. **Configure SPF Record:**
    - Add an SPF TXT record to your domain:
        - **Name:** `@`
        - **Type:** TXT
        - **Value:** `v=spf1 ip4:YOUR_BLOCK_IP_ADDRESS/30 -all`

4. **Place the DKIM Private Key:**
    - Move the `smtp.private` file to the project as `./dkim/yourdomain.com.private` so that the server can access it for signing emails.

### Project Startup

1. **Navigate to the Docker Directory:** Go to the Docker setup folder.
   ```bash
   cd ./docker
   ```

2. **Build Docker Images:** Run the `build.sh` script to build the Docker images.
   ```bash
   ./build.sh
   ```

3. **Start the Project:** Execute the `up.sh` script to start the project. After startup, an API key will be displayed in the console output for access.
   ```bash
   ./up.sh
   ```
   **Example Console Output:**
   ```plaintext
     ...

      INFO  API token is [918f89c2dc8e9bdbd7e8e4338c9e8be9].  
   ```

### API Usage

The API exposes endpoints to manage messages and recipients.

#### Authorization

All API requests must include an authorization token in the headers. Use the following format:

```
Authorization: Bearer YOUR_API_TOKEN
```

Replace `YOUR_API_TOKEN` with the API token displayed upon project startup.

#### 1. Create a Message
- **Endpoint:** `POST http://localhost:8081/api/v1/messages`
- **Headers:** Include the authorization header with the token.
  ```http
  Authorization: Bearer YOUR_API_TOKEN
  ```
- **Payload:**
  ```json
  {
    "dkim_signer_domain": "yourdomain.com",
    "dkim_signer_sector": "smtp",
    "from_title": "YourTitle",
    "from_email": "no-reply@yourdomain.com",
    "subject": "Your Subject",
    "replay_to": "reply@yourdomain.com",
    "body": "Email body content"
  }
  ```
- **Response Example:**
  ```json
  {
    "message": {
      "id": 1,
      "dkim_signer_domain": "yourdomain.com",
      "dkim_signer_sector": "smtp",
      "from_title": "YourTitle",
      "from_email": "no-reply@yourdomain.com",
      "replay_to": "reply@yourdomain.com",
      "subject": "Your Subject",
      "body": "Email body content",
      "created_at": "2024-10-31 14:35:39.296672",
      "updated_at": "2024-10-31 14:35:39.296672"
    }
  }
  ```

#### 2. Add Recipients to a Message
- **Endpoint:** `POST http://localhost:8081/api/v1/messages/{message_id}/recipients`
- **Headers:** Include the authorization header with the token.
  ```http
  Authorization: Bearer YOUR_API_TOKEN
  ```
- **Payload:**
  ```json
  {
    "recipients": [
      { 
        "email": "recipient1@example.com",
        "replacements": [
          {
            "search": "search",
            "replace": "replace"
          }
        ] 
      },
      { "email": "recipient2@example.com" }
    ]
  }
  ```
- **Response Example:**
  ```json
  {
    "recipients": [
      {
        "id": 1,
        "message_id": 1,
        "batch_id": "unique-batch-id",
        "email": "recipient1@example.com",
        "status": "created",
        "replacements": [],
        "debug": null,
        "postfix_id": null,
        "postfix_status": null,
        "postfix_response": null,
        "created_at": "2024-10-31 14:35:42.336293",
        "updated_at": "2024-10-31 14:35:42.336293"
      },
      ...
    ]
  }
  ```

#### 3. Check Message Status
- **Endpoint:** `GET http://localhost:8081/api/v1/messages/{message_id}/recipients`
- **Headers:** Include the authorization header with the token.
  ```http
  Authorization: Bearer YOUR_API_TOKEN
  ```
- **Optional Parameter:** You can include the `status` parameter to filter recipients based on their delivery status, with values `"failed"` or `"delivered"`.
- **Response Example:**
  ```json
  {
    "recipients": [
      {
        "id": 1,
        "email": "recipient1@example.com",
        "status": "delivered",
        "updated_at": "2024-10-31 14:39:34.284041",
        "postfix_status": "sent",
        "postfix_response": "250 ok 1730385564 qp 30909"
      },
      ...
    ]
  }
  ```

#### 4. Check Message Sending Progress
- **Endpoint:** `GET http://localhost:8081/api/v1/messages/{message_id}/progress`
- **Headers:** Include the authorization header with the token.
  ```http
  Authorization: Bearer YOUR_API_TOKEN
  ```
- **Response Example:**
  ```json
  {
    "progress": "100.00"
  }
  ```
