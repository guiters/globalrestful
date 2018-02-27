# Global RestFul
### Hello, World this is a most userfull api eveeer

#### Here you can use as Source

 - MySQL Server
 - PostgreSQL Server
 - Oracle server
 - Outside API's
 - Other database servers
 - Create your ower driver

Using a simple interface we prefer the ambiance KISS (Keep It Simple N Stupid), To anyone create  a api to any application

All the system is based in the PATTERN Schema Creating a PATTERN JSON file the system is automatic read and process this pattern in the best way.

We show to you an example:

	{  
	  "drive": "DriveMysql",  
	  "database": "MyCompany",  
	  "table": "Employers",  
	  "columns": [
		  "id",
		  "document"
	  ],
	  "limitpage": "100",  
	  "where": {
		  "require": {
			  "id": "/^\[0-9\]*$/"
	      },
	      "optional": {
		      "document": "/^\[0-9\]*$/"
		  }
	   },
	   "requires": {
		   "REQUEST": [
		   "GET",
		   "POST",
		   "PUT",
		   "DELETE"
		   ]
		}
	}

### Now i will explain this

 1. drive = Drive of connection with the source
 2. database = The database in de source *optional for no database source
 3. table = The table in the source
 4. columns = The columns to show in the response this is an array
 5. limitpage = the limit to show in each page
 6. where = the request avalible to user search the data
	 1. require = An array of columns with regex validation for each request
	 2. optional = An array  of columns with regex validation not required
7. requires = an array of Requires for all request
	1. REQUEST = Array of all request type available


----------


### How Works ?
![Diagram](https://lh5.googleusercontent.com/4lS-AHyeaW2tLWgFxr970axzfQx8_4tF_SCRLtYRONa6EYlsic01HtuOAT3I_vbGpiK6wx4hBVXVPHQggF8z=w1920-h960)