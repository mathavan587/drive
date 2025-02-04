Accessing Google Drive using PHP is typically done via the [Google API PHP Client Library](https://github.com/googleapis/google-api-php-client). Below is a step-by-step guide on how to set up and use this library to access Google Drive from your PHP application.

---

## 1. Set Up Google Cloud Project and Enable the Drive API

1. **Create a Google Cloud Project:**
   - Go to the [Google Cloud Console](https://console.cloud.google.com/).
   - Create a new project or select an existing one.

2. **Enable the Google Drive API:**
   - In your project dashboard, navigate to **APIs & Services → Library**.
   - Search for **Google Drive API** and click **Enable**.

3. **Create OAuth 2.0 Credentials:**
   - Go to **APIs & Services → Credentials**.
   - Click **Create Credentials** and choose **OAuth client ID**.
   - If prompted, configure the consent screen.
   - Select **Web application** as the Application type.
   - Add your authorized redirect URIs (for example, `http://yourdomain.com/oauth2callback.php`).
   - Save your **Client ID** and **Client Secret** for later use.

---

## 2. Install the Google API PHP Client Library

The recommended way to install the Google API PHP Client is using Composer.

1. **Install Composer** (if not already installed) by following the instructions at [getcomposer.org](https://getcomposer.org/).

2. **Install the Library:**

   Open your terminal in your project’s root directory and run:

   ```bash
   composer require google/apiclient:"^2.0"
   ```

3. **Include the Composer Autoloader in Your PHP Script:**

   ```php
   require_once 'vendor/autoload.php';
   ```

---

## 3. Authenticate and Access Google Drive in PHP

Below is a basic example of how to authenticate and list files from Google Drive using PHP.

### Example: Listing Files in Google Drive

1. **Create an OAuth Callback Script (e.g., `oauth2callback.php`):**

   This script handles the OAuth 2.0 flow. Replace `'YOUR_CLIENT_ID'` and `'YOUR_CLIENT_SECRET'` with your credentials.

   ```php
   <?php
   require_once 'vendor/autoload.php';
   session_start();

   $client = new Google_Client();
   $client->setClientId('YOUR_CLIENT_ID');
   $client->setClientSecret('YOUR_CLIENT_SECRET');
   $client->setRedirectUri('http://yourdomain.com/oauth2callback.php');
   $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

   // If we have a code back from the OAuth flow, we need to exchange that with the OAuth token.
   if (isset($_GET['code'])) {
       $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
       // Save the token to the session for later use.
       $_SESSION['access_token'] = $token;
       header('Location: ' . filter_var('index.php', FILTER_SANITIZE_URL));
       exit;
   }

   // If there is an access token in the session, set it.
   if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
       $client->setAccessToken($_SESSION['access_token']);
   } else {
       // If no token, generate an auth URL and redirect to it.
       $authUrl = $client->createAuthUrl();
       header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
       exit;
   }

   // Redirect back to your main script after authentication.
   ```

2. **Create a Main Script to Access Google Drive (e.g., `index.php`):**

   This script uses the access token stored in the session to access the Drive API and list files.

   ```php
   <?php
   require_once 'vendor/autoload.php';
   session_start();

   $client = new Google_Client();
   $client->setClientId('YOUR_CLIENT_ID');
   $client->setClientSecret('YOUR_CLIENT_SECRET');
   $client->setRedirectUri('http://yourdomain.com/oauth2callback.php');
   $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

   // Check if the access token exists
   if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
       $client->setAccessToken($_SESSION['access_token']);
   } else {
       // Redirect to OAuth if no access token is found.
       header('Location: oauth2callback.php');
       exit;
   }

   // Create the Google Drive service instance
   $service = new Google_Service_Drive($client);

   // List files from Google Drive
   try {
       $results = $service->files->listFiles([
           'pageSize' => 10,
           'fields' => 'nextPageToken, files(id, name)'
       ]);

       if (count($results->getFiles()) == 0) {
           print "No files found.\n";
       } else {
           print "Files:\n";
           foreach ($results->getFiles() as $file) {
               printf("%s (%s)\n", $file->getName(), $file->getId());
           }
       }
   } catch (Exception $e) {
       echo "An error occurred: " . $e->getMessage();
   }
   ?>
   ```

### How It Works:
- **OAuth Flow:**  
  When you visit `index.php`, the script checks for a valid access token in the session. If it isn’t available, it redirects you to `oauth2callback.php` where you are prompted to authenticate with Google.  
- **Token Storage:**  
  After successful authentication, the token is stored in the session.  
- **Accessing the API:**  
  Once authenticated, the script creates a `Google_Service_Drive` instance using the authenticated client and performs API calls (in this case, listing files).

---

## 4. Additional Operations

Once you have set up the basic access, you can perform additional operations such as:
- **Uploading Files:**  
  Use `$service->files->create()` with proper metadata and file content.
- **Downloading Files:**  
  Use `$service->files->get($fileId, ['alt' => 'media'])` to download file content.
- **Updating or Deleting Files:**  
  Use the corresponding methods from the Google Drive API.

You can refer to the [Google Drive API PHP Reference](https://developers.google.com/drive/api/v3/reference/) for more details on available operations.

---

## Summary

1. **Set Up Google Cloud & Enable API:** Create a project, enable the Drive API, and create OAuth credentials.
2. **Install the PHP Client Library:** Use Composer to add the `google/apiclient`.
3. **Implement OAuth Flow:** Use scripts like `oauth2callback.php` to authenticate and store the access token.
4. **Access Google Drive:** Create a service instance and perform API operations (like listing files).

This basic setup should help you get started accessing Google Drive from your PHP application. If you have further questions or run into any issues, feel free to ask!