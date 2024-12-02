# Svalberg-Motell

## Important Information for Mac Users

When using this project on macOS, please note that certain folders require specific permissions to ensure the application functions as expected. Follow the steps below to configure the necessary permissions.

1. Navigate to the `svalberg-motell` project folder in the terminal:
   ```
   cd /Applications/XAMPP/htdocks/svalberg-motell
   ```

2. Set up the file and folder permissions as follows:
   ```
   chmod 666 www/private/log/log.txt
   ```

   ```
   chmod 666 www/tmp
   chmod 7777 www/tmp
   ```

   



