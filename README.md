#Club Buddy API v0.3

#####API Base Url
	http://hackathon.fernandomendez.io/
	
######Notice  
	Accessing the base URL directly without any API methods will result in a *404* status code being shown.  

#API End Points and Methods    

####/check/\<10 digit phone number\>/
	Checks the phone number provided to check if user is already registered
	Returns (Registered = 1 if registered or Registered= 0 if not registered)
	
####/register/\<10 digit phone number\>/ \<4 digit pin code\>
	Creates a new token for a new user using the 10 digit phone and pin
####/verify/\<token\>/
	Checks the token provided to make sure it is a valid user token. 
	Tokens must be md5((User’s 10-Digit Phone) + (User’s Pin))
	Returns (Valid = 1 if valid, Valid = 0 if not valid)

####/location/add/\<token\>/ \<JSON encoded HomeLocation Object\>
	Adds or updates the home location for user whose token is provided
####/location/get/\<token\>
	Returns JSON encoded HomeLocation Object
####/friend/add/\<token\>/\<friend id\>
    Adds the user specified by the friend ID (10 digit phone number) 
    to friend list of user whose token was specified
####/friend/remove/\<token\>/\<friend id\>
Remove the user specified by the friend ID (10 digit phone number) 
to friend list of user whose token was specified
####/session/add/\<token\>/\<userid to invite\>
    Invites a user to a clubbing session ‘activesession’, to invite a 
    user that user must have the host on their friends list (host only)
    Returns session object containing updated friends information
####/session/create/\<token\>
    Creates new clubbing session and sets it as the user whose token was provided ‘activesession’ and as the host.
    Returns session object containing information of the new session
####/session/close/\<token\>/
    Closes the session and marks it as inactive (host only)
    Returns session object containing information of the now closed session
####/session/remove/\<token\>/
    Remove a friend who is no longer in the session (host only)
    Returns session object containing updated friends information

#API JSON Classes  

###Session
	id 		    – id of the session
	active 		– state of the session (1 = active, 0 = inactive / closed)
	host 		  – id of the session owner
	friends		- Object containing the ID’s of all friends in the sessions [index] =\> [userID]
	sessiontime 	– time at which session was created

###User
	id 		       – id of the user
	currentsession – id of the current session the user is in (0 if none)
	home 		   – HomeLocation Object (see below) of the users home location

###HomeLocation 
	lat 		 – latitude of user’s home location
	long		 – longitude of user’s home location

###Friends (Used both in Session and User class)
	Index		- index, in order of who was added first.
	UserID		- User ID of all friends
		
