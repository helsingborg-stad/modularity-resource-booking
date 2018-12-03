# Modularity Resource Booking

Book a resource for a predefined period of time. This plugin provides a api-based frontend. 

## REST API
This plugin creates a complete API to integrate any frontend solution. The plugin is deliverd with a local frontend solution, but can work with a offsite frontend solution. Listed below is a index of possible endpoints, these should be prefixed with your json-url in WordPress, for example https://develop.local/wp-json/. 

For detailed documentation of the API, please refer to our Postman page at https://documenter.getpostman.com/view/5930358/RzffHp48 . 

### Users / Customer

* ``` ModularityResourceBooking/v1/UserEmailExists ``` - Check if a email exists $_POST['email'] must be set. 
* ``` ModularityResourceBooking/v1/CreateUser ``` - Create a user
* ``` ModularityResourceBooking/v1/ModifyUser/ID ``` - Modifies logged in user

### Products

* ``` ModularityResourceBooking/v1/Product/ ``` - List all avabile products. 
* ``` ModularityResourceBooking/v1/Product/ID ``` - Get a single product. 

### Package

* ``` ModularityResourceBooking/v1/Package/ ``` - List all avabile package. 
* ``` ModularityResourceBooking/v1/Package/ID ``` - Get a single package. 

### Order / Purchase

* ``` ModularityResourceBooking/v1/Order/ ``` - List all avabile orders. 
* ``` ModularityResourceBooking/v1/Order/ID ``` - Get a single order. 
* ``` ModularityResourceBooking/v1/ModifyOrder/ID ``` - Update a order owned by the user. 
* ``` ModularityResourceBooking/v1/RemoveOrder/ID ``` - Remove a order owned by the user. 

### Time slots

* ``` ModularityResourceBooking/v1/Slots ``` - List all slots, avabile and unavabile.

## Why is the order posttype called purchase? 
You cannot name a posttype "order" in WordPress. This is a reserved keyword that will break all post-listings. We have renamed it to "purchase" but still want to present the name as "order" due to simplicity & logical reasons for the user. 
