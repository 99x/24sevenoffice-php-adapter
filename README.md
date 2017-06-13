# 24SevenOffice-PHP-Adapter
A PHP Adapter to use in integrations with https://24sevenoffice.com/ CRM platform

## Getting Started

Use the config.php to set the 24sevenoffice credentials http://developer.24sevenoffice.com/dev/#api-intro,
```php
      $username = "USER";  //Change this to your client user or community login
      $password = "PASS";  //Change this to your password
      $applicationid = "APP_ID";  //Change this to your applicationId
```

## Functions

To call the API endpoint use a POST to the index document
```
http://YourHost/index.php?type=TYPE
```
---
### Save Companies
Use the **type=new**

To save a company to the CRM you can use the following method and add the needed fields,

```php
        $company["companies"][0]['Name'] ="NAME";
        $company["companies"][0]['Type'] ="Business";
        $company["companies"][0]['OrganizationNumber'] ="ORG_ID";
```

#### Request Body: 
```javascript
{
	"Name" : "NAME TO SENT",
	"Email" : "EMAIL ADD",
	"Account_ID" : "A123"
}
```

#### Response:
**Success** Will return the added organization

```javascript
{
    "status": 1,
    "data": {
        "Id": 46,
        "OrganizationNumber": "A123",
        "Name": "NAME TO SENT",
        "EmailAddresses": {
            "Invoice": {
                "Description": "EMAIL ADD",
                "Name": "EMAIL ADD",
                "Value": "EMAIL ADD"
            },
            "Work": {
                "Description": "EMAIL ADD",
                "Name": "EMAIL ADD",
                "Value": "EMAIL ADD"
            }
        },
        "Type": "Business",
        "LedgerCustomerAccount": 0,
        "LedgerSupplierAccount": 0
    },
    "identity": "{CURRENT IDENTITY OBJECT}"
}
```

**Fail** Will return the error with the status code of 1

Sample: 
```javascript
{
    "status": 0,
    "data": "Property Type is missing.",
    "identity": "{CURRENT IDENTITY OBJECT}"
}
```
---
For more data fields http://developer.24sevenoffice.com/diverse/apicompanyservice-datatypes/


### TODO

- Create a REST Endpoint to call the functions directly
- Make the functions run independently on requests
