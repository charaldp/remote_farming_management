Setting Up:
- Run ```composer-install```
- Run ```cp .env.example .env``` (copy enviroment file which is not included to the repository)
- Set the ```DB_DATABASE```, ```DB_USERNAME``` and ```DB_PASSWORD``` to match with the database
- Run ```npm install```

Run to build (not for the first time only)
- ```php artisan migrate:fresh --seed```: Run the migrations for the database and then run the seeds for inserting sample data to the database)
- ```npm run watch```: Run the javascript building script which is builded into a file at /public/js/app.js. This remains running and watches for changes in order to rebuild the file automatically when changes occur

Running required for serving the app with all features enabled
- ```php artisan serve --host=your.local.ip.address```
- ```php websockets:serve```
- ```php artisan queue:work```
