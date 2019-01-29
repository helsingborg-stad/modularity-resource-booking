# Modularity Resource Booking

Book a resource for a predefined period of time. This plugin provides a api-based frontend. 

## Developement mode
You can temporarily disable all security features by defining 'RESOURCE_BOOKING_DISABLE_SECURITY' to 'true' in your wp-config.php file. This bypasses most check that looks capabilities or logged in users.  

## Constants
- RESOURCE_BOOKING_CURRENCY_SYMBOL - Defines a currency symbol appended to currency. 

## REST API
This plugin creates a complete API to integrate any frontend solution. The plugin is deliverd with a local frontend solution, but can work with a offsite frontend solution. Listed below is a index of possible endpoints, these should be prefixed with your json-url in WordPress, for example https://develop.local/wp-json/. 

For detailed documentation of the API, please refer to our Postman page at https://documenter.getpostman.com/view/5930358/RzffHp48 . 

### Nonces
All requests interacting with user bound data this api requires a nonce field to be posted with the request. Documentation here: https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/

### Users / Customer

* ``` ModularityResourceBooking/v1/CreateUser ``` - Create a user
* ``` ModularityResourceBooking/v1/ModifyUser/ID ``` - Modifies logged in user

### Products

* ``` ModularityResourceBooking/v1/Product/ID ``` - Get a single product. 

### Package

* ``` ModularityResourceBooking/v1/Package/ID ``` - Get a single package. 

### Order / Purchase

* ``` ModularityResourceBooking/v1/MyOrders ``` - Get current users orders. 
* ``` ModularityResourceBooking/v1/CreateOrder ``` - Create an order. 
* ``` ModularityResourceBooking/v1/CancelOrder/ID ``` - Cancels an order owned by the user. 

### Time slots

* ``` ModularityResourceBooking/v1/Slots ``` - List all slots, available and unavailable.

## Why is the order posttype called purchase? 
You cannot name a posttype "order" in WordPress. This is a reserved keyword that will break all post-listings. We have renamed it to "purchase" but still want to present the name as "order" due to simplicity & logical reasons for the user. 
