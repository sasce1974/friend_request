<h1>REST API "FRIEND REQUEST"</h1>

<h2>INTRODUCTION</h2>

This API has the following main functionality:
<ul>
    <li>register and log in users using Laravel built in laravel/ui Composer package;</li>
    <li>show all users;</li>
    <li>show one user;</li>
    <li>create user;</li>
    <li>update user;</li>
    <li>delete user;</li>
    <li>show all user friends;</li>
    <li>create friend request;</li>
    <li>receive friend request;</li>
    <li>accept friend request;</li>
    <li>reject friend request and</li>
    <li>cancel friend request.</li>
</ul>
<p>As a security option there is a Basic Authentication middleware that allows actions only to registered users.</p>
<p>Aditionally to above functionalities, the built in laravel functions as database migration allow easely to initialize the project.</p>

<h2>1. INSTALATION</h2>

Before starting with this project, the following dependecies are REQUIRED:
<ul>
    <li>composer</li>
    <li>node.js</li>
    <li>local server</li>
    <li>PHP v.7.3</li>
    <li>MySQL </li>
</ul>
This project can be downloaded from the Github repository. If you are usin Git on your CLI, the pull request CAN be done with the following line:

	git clone https://github.com/sasce1974/friend_request.git

After the project is downloaded locally, additional program dependencies for the Laravel project MUST be installed. Please run into your CLI:

	composer install

After that:

	npm install

Create a copy from the .env.example file from the root folder of the project and name it .env
Can be done it in the CLI with the command:

	cp .env.example .env

The .env file (environment file) contains all the basic configurations for the program to run. 

Generate an App Encryption Key:

	php artisan key:generate

This will create an app key string into the APP_KEY setting of the .env

Create empty database for the application.

Add the details for the database connection to the .env file <i>(Host, database name, username and password)</i>

Migrate the database with the following code in your CLI:

	php artisan migrate

This WILL generate the tables in the created database: <b>users</b> and <b>friends</b>, along with the tables needed for the Authentication.

Note: In the older versions of MySQL, migration can throw the following Error:
<i>ERROR SQL STATE [42000]: Syntax error or access violation: 1701 Specified key was too long; max key length is 767 bytes</i>

In such case, you can refer to the following post: https://laravel-news.com/laravel-5-4-key-too-long-error

Please set up your virtual host to the 'C:\{PathTo}\{MyProject}\Public' directory of the project as a base.

With this, the API is ready to be used on the local machine.

<h2>2. MODELS</h2>

Aside from the program framework, this project contains two main models: User and Friends with each represented with one table in the database.

<h3>2.1. Model User</h3>

The User Model is a default Laravel prebuilt model with a tabele defined with the columns:

- id (autoincrement/primary)
- name (string)
- email (string)
- password (string)
- created_at (timestamp)
- updated_at (timestamp)


<h3>2.2. Model Friends</h3>

The only modification in this Model is inserted columns names into the '$fillable property' as a requirements for the Laravel Eloquent to manipulate the data in those columns of the friends table. 
This model use the friends table which is only ment to produce a MANY TO MANY relationship within every user.

The core "friendship" logic in this application runs trough this table. It contains the following columns:

- id (autoincrement/primary)
- user_one (integer)
- user_two (integer)
- accepted (boolean)
- rejected (boolean)
- created_at (timestamp)
- updated_at (timestamp)


<h4>Friend Request Logic:</h4>

The column <i>'user_one'</i> takes the id of the user that makes a friend request.

The column <i>'user_two'</i> takes the id of the user that is requested.

Column <i>'accepted'</i> by default is 0, and when user accept the request, it become 1.

Same is for the column <i>'rejected'</i>.

If the user wants to cancel his friend request, simply deletes the friend request record. That only the user that created the request can do.

More in details about each action will be explained bellow with the Route - Controller interaction.


<h2>3. ROUTES (ENDPOINTS) AND CONTROLLERS</h2>

The API routes are defined in the routes directory: {PROJECT}\routes\api.php

The main API url is: http://{yourhostname}/api/ following with the name of the model (user or friend)

In the api.php you can find two grops of defined routes, one group for each Controller:

<h3>3.1. UserController</h3>

UserController via the routes bellow have the following functionality:
- index - return all the users
- show - return one user data
- create user
- update user
- delete user

Routes that employs the above functions are:

    Route::get('/user', 'UserController@index');
    Route::get('/user/{id}', 'UserController@show');
    Route::post('/user', 'UserController@create');
    Route::put('/user/{id}', 'UserController@update');
    Route::delete('/user/{id}', 'UserController@delete');


<u>GET 'http://{yourhostname}/api/user'</u> 

returns a json format of data of all the users.


<u>GET 'http://{yourhostname}/api/user/{id}' </u>

returns a json format of data of the user with id as in the parameter.


<u>POST 'http://{yourhostname}/api/user' </u>

sends a post with a new user data and creates new record in the user table. The data first is validated with the following roules:

- name: required, max 100 char.
- email: required, must be email and unique,
- password: is required, between 8 and 20 char.

If the validation fails, this function returns appropriate error in a json format.

The password is encrypted with the bcrypt() function.

The <i>'create'</i> method on success returns the instance of the new created user in json format.


<u>PUT 'http://{yourhostname}/api/user/{id}'</u> 

updates the user with the ID as the param from the route. 

Similar to the previous method, data is validated for the fields that are passed in the request with the same rules.
If the validation fails, this function returns appropriate error in a json format.

The password is encrypted with the bcrypt() function.

The <i>'update'</i> method on success returns the instance of the updated user in a json format.


<u>DELETE 'http://{yourhostname}/api/user/{id}' </u>

deletes the user with ID as the param from the route. On success returns the 204 status code.

NOTE: 
Above functions returns error codes 40X appropriately, or 20X on success.

Also, the "sensitive" functions like (create, update, delete) contain a mockup method 'isAuthorized' from the User model. This function can be employed as a security add to protect the data change from unauthorized persons, but after the eventual improving the application with more functionality (e.g. User levels, user roles...) and setting that to this function. For now it is set to return only <b>true</b>.

 
<h3>3.2. FriendsController</h3>

FriendsController via the routes bellow have the following functionality:
- friends - return all the user friends
- create friend request
- get friend request
- accept friend request
- reject friend request
- cancel friend request

Routes that employs the above functions are:

    Route::get('/friend/{id}', 'FriendsController@friends');
    Route::post('/friend/{id}', 'FriendsController@createFriendRequest');
    Route::get('/friend', 'FriendsController@getFriendRequests');
    Route::put('/friend/accept/{id}', 'FriendsController@acceptFriendRequest');
    Route::put('/friend/reject/{id}', 'FriendsController@rejectFriendRequest');
    Route::delete('/friend/{id}', 'FriendsController@cancelFriendRequest');


<u>GET 'http://{yourhostname}/api/friend/{id}' </u>

returns a json format of:
- friend request id - that connects the users
- user ID, name and email

on the users that are friends with the user with the passed parameter {id}.
Column <i>'user_one'</i> from the <i>'friends'</i> table have a records the user id that is sending the friend requests.
Column <i>'user_two'</i> from the <i>'friends'</i> table have a records from the user id to whom the friend requests is sent.



<u>POST 'http://{yourhostname}/api/friend/{id}' </u>

This route use param {id} to send friend request to an user with id:{id} by storing it in the <i>'user_two'</i> column of the table <i>'friends</i>' 
The ID of the authenticated user is used in the first query to check if there is no existing friend request to the same user, and after, to save it in the first column <i>(user_one)</i>


<u>GET 'http://{yourhostname}/api/friend' </u>

returns all friend requests towards the authenticated user - All the columns from the affected records from the friends table and the users ID (that is equal to <i>user_one</i>), emails and names from the users table.



<u>PUT 'http://{yourhostname}/api/friend/accept/{id}' </u>

This route creates an object from the friend request by the provided param {id} (id of the friend request record), checks if the friend request exists and if it is related to the authenticated user, then updates the <i>'accepted'</i> column from the <i>'friends'</i> table and returns status 200.
If authenticated user is not the the same as in column 'user_two', status 403 is returned.



<u>PUT 'http://{yourhostname}/api/friend/reject/{id}' </u>

This route creates an object from the friend request by the provided param {id} (id of the friend request record), checks if the friend request exists and if it is related to the authenticated user, then updates the <i>'rejected'</i> column from the <i>'friends'</i> table and returns status 200.
If authenticated user is not the the same as in column <i>'user_two'</i>, status 403 is returned.



<u>DELETE 'http://{yourhostname}/api/friend/{id}' </u>

This route creates an object from the friend request by the provided param $id (id of the friend request record), checks if the friend request exists and if it is created by the authenticated user, then deletes that record from the <i>'friends'</i> table and returns status 200.
If authenticated user is not the the same as in column 'user_one', status 403 is returned.


NOTE: 
Above functions returns error codes 40X and 500 appropriately or 200 on success.

