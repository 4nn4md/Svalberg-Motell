# Svalberg-Motell
Creators: Anna Maria Dang and Ine Antonsen 

## Important Information for Mac Users

When using this project on macOS, please note that certain folders require specific permissions to ensure the application functions as expected. Follow the steps below to configure the necessary permissions.

1. Navigate to the `svalberg-motell` project folder in the terminal:
   ```
   cd /Applications/XAMPP/htdocks/svalberg-motell
   ```

2. Set up the file and folder permissions as follows:
   ```
   chmod 666 private/log/log.txt
   ```

   ```
   chmod 666 www/tmp
   chmod 777 www/tmp
   ```

## Description
This project is a room booking system for a small motel with 25 rooms, designed to enhance the booking experience for both guests and administrators. The system includes six room types, each with specific capacity limits for adults and children, ensuring a tailored booking experience.

Guests can register, search for available rooms by check-in/out dates, specify the number of people, and filter based on preferences such as floor level or proximity to an elevator. Administrators can define room types, adjust room availability, and manage bookings effectively.

The system emphasizes clean code and user-friendly design, utilizing **PHP** for backend functionality and integrating **Bootstrap 5** and **JavaScript** to deliver a modern and responsive interface.

## Instructions
**Step 1**: Clone the Repository
Open your terminal and navigate to the `htdocs` directory inside your XAMPP installation folder:
   ```
   cd /Applications/XAMPP/htdocs
   ```

Clone the repository to your local machine:
   ```
   git clone <repository-link>
   ```

**Step 2**: Start the Server
1. Open the XAMPP control panel.
2. Start both **Apache** and **MySQL** services.

**Step 3**: Set Up the Database in phpMyAdmin
1. Open phpMyAdmin in your web browser by navigating to `http://localhost/phpmyadmin`.
2. Create a new database named `svalberg_motell`.
3. Import the database schema by selecting **Import** and choosing the `svalberg_motell.sql` file located in `XAMPP/htdocs/Svalberg-Motell/`.
4.  Once the server is running, open a web browser and go to:
   ```
   http://localhost/svalberg-motell/index1.php
   ```


**Step 4**: Log In
- Admin login:
   - Email: `admin@svalberg.no`
   - Password: `Admin123!`

- Guest login:
   - Email: `adang@hotmail.com`
   - Password: `Test1234!`


## Installation Instructions

This project is written in PHP and requires XAMPP and Visual Studio Code (VS Code) for development. Below are the steps to set up the development environment on both** PC** and **Mac**.

**Step 1: Install Required Software**
1. **XAMPP** (not the VM version): This provides the Apache server, MySQL, and PHP, which are required to run the project locally.
   - **Download XAMPP**:
      XAMPP Download
2. **Visual Studio Code (VS Code)**: A lightweight and powerful code editor.
   - **Download Visual Studio Code**:
      [VS Code Download](https://code.visualstudio.com/)

**Step 2: Install Extensions in Visual Studio Code**
Once you've installed Visual Studio Code, open it and install the following extensions:

1. **PHP Intelephense**: A PHP IntelliSense extension that provides autocompletion, error checking, and more for PHP code.
   - To install, go to the **Extensions** tab in VS Code and search for PHP Intelephense. Click **Install**.
2. **MySQL (Client for VS Code)**: An extension to interact with MySQL databases directly from within VS Code.
   - To install, go to the **Extensions** tab in VS Code and search for MySQL (Client for vscode). Click **Install.**

**Step 3: Open the XAMPP Control Panel**
**Start Apache and MySQL** services from the XAMPP control panel. This will enable the local server and database necessary to run the PHP project.
   - On Windows, the XAMPP Control Panel should open automatically after installation.
   - On Mac, open XAMPP from the Applications folder and start both Apache and MySQL.

**Step 4 (Optional): Change MySQL Root Password**
This step is optional unless you need to set a custom password for the MySQL root user.
1. Open the XAMPP Control Panel.
2. Click **Admin** next to MySQL to open **phpMyAdmin**.
3. If you want to change the MySQL root password, follow this guide to reset the password:
   [Change MySQL Root Password](https://kinsta.com/knowledgebase/xampp-mysql-password/)
4. After changing the MySQL password, find the file config.inc.php located in xampp/phpMyAdmin/.

Edit the following line in the file:
```
$cfg['Servers'][$i]['password'] = 'your_new_password';
```

5. Replace your_new_password with the password you've set.


**Step 5: Verify Installation**
- Once XAMPP is installed and running, navigate to http://localhost in your browser. You should see the XAMPP dashboard, confirming that the Apache server is running.
- You can place the project folder in the htdocs directory inside your XAMPP installation folder to test it locally. This directory is where XAMPP looks for files to serve.

**Step 6: Starting the Project**
1. Place your PHP project files inside the htdocs directory (located in your XAMPP installation folder). For example:

C:/xampp/htdocs/Svalberg-Motell/

or for Mac:

/Applications/XAMPP/htdocs/Svalberg-Motell/

2. Open your browser and go to http://localhost/Svalberg-Motell/ to view the project.

**Troubleshooting and Tips**
- **Apache or MySQL not starting?**
   If Apache or MySQL is not starting in XAMPP, make sure no other services are using the same ports. You may need to change the port number in XAMPP settings (e.g., changing Apache's port to 8080).

By following these steps, you should be able to set up the development environment and run the PHP project on your local machine.

## API Integration: UI Avatars
This project uses the UI Avatars API to dynamically generate avatar images based on users' first and last initials. The avatars are displayed on the user's profile page, enhancing the user experience by providing a visual representation.

**How It Works**
The UI Avatars API generates customizable avatar images by using the initials of a person's name. In this project, the avatar is generated based on the user's first and last name initials.

**API Endpoint**
The base URL for UI Avatars is:
https://ui-avatars.com/api/

**Parameters Used**
- **name:** The name for which the avatar will be generated. In this project, we use the initials from the user's first and last name.
- **size:** Specifies the size of the avatar image in pixels. The default size is 128px, but in this project, we use 256px.
- **background:** Defines the background color of the avatar. In this project, the background color is set to a shade of blue (#4e73df).
- **color:** Defines the color of the initials (text) on the avatar. The text color in this project is white (#ffffff).
- **rounded:** Specifies whether the avatar should have rounded corners. In this project, we use true to make the avatar rounded.

**Example Request**
For a user with the first name "John" and last name "Doe," the following URL would generate an avatar with the initials "JD":
https://ui-avatars.com/api/?name=JD&size=256&background=4e73df&color=ffffff&rounded=true

This will return an image of the avatar with a blue background and white initials.

**Code Example**
In the PHP code, the avatar URL is dynamically generated using the first and last initials of the user's name. Here's the relevant code snippet:

```
// Generate the avatar URL dynamically based on the first letter of first name and last name
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($firstName[0] . $lastName[0]) . "&size=256&background=4e73df&color=ffffff&rounded=true";
```

In this code, the first letter of the firstName and lastName is used to generate the initials, which are passed to the UI Avatars API to generate the avatar.

**Avatar Display**
Once the avatar is generated, it is displayed in the user's profile section on the web page. The image is shown with a size of 150px.
```
<img src="<?php echo $avatarUrl; ?>" alt="Profile picture of <?php echo $firstName . ' ' . $lastName; ?>" class="img-fluid img-thumbnail mt-4 mb-2" style="width: 150px;">
```

**Why Use UI Avatars?**
UI Avatars provides a simple, quick way to generate personalized avatars without requiring any image upload or complex design. It is lightweight, customizable, and perfect for applications that need to display avatars based on user initials.

**Rate Limiting**
As of the last check, UI Avatars does not impose strict rate limits on its usage, but it’s recommended to avoid excessive requests to prevent throttling. This API is free for public use.







## API Integration: SMS
This project uses the Twilio API to send SMS messages. To get started, you need to have PHP and Composer installed on your system. Here is a step-by-step guide on how to set up and use the project.

https://www.twilio.com/en-us

**Prerequisites**
Before you can use this project, make sure you have the following tools installed on your system:

- **PHP:** This project is written in PHP, so you need PHP to run the code. You can download and install PHP from php.net. Ensure that you have PHP version 7.3 or higher.
- **Composer:** Composer is a dependency manager for PHP, and we use it to install the Twilio SDK. If you don’t have Composer installed, you can download it from getcomposer.org.
After installation, you can check if Composer is working by running composer --version in the terminal to confirm that Composer is installed correctly.

**Setting up the project:**
To send messages using Twilio, you'll need to have a **Twilio account**. After registering on Twilio, navigate to the **Twilio Console** to find your **Account SID** and **Auth Token**. These credentials are essential for authenticating your API requests. Replace the placeholder values in your code with your actual Twilio credentials.

1. **Locate the file to modify:** Go to the controller/genereateSMS.php where the SMS sending functionality is implemented
2. **Update the credentials:** In this file, replce the placeholder values with your actual Twilio credentials. 


For example, in the file where you send SMS messages, replace the following lines:
 ```
$sid = 'TWILIO_SID';  // Replace with your Twilio Account SID
$token = 'TWILIO_AUTH_TOKEN'; // Replace with your Twilio Auth Token
$client = new Client($sid, $token);
 ```
Use this if you dont have an account and cant make one:
 ```
$sid = 'AC2d5d6a5ae7ead2afbf566af96efdea8c'; // Replace with your Twilio Account SID
$token = '250b66ad672f0cb0cad6f90b0e928117'; // Replace with your Twilio Auth Token
$twilio_number = '+1 567 229 8275'; // Your Twilio phone number
 ```
**Important Information:**
- PHP is required for the code to function, as the project is written in PHP.
- Composer is required to manage the dependencies, but if you have cloned the repository and the vendor folder is included, you don’t need to install Twilio SDK again. You can simply run composer install to ensure all necessary dependencies are in place.




