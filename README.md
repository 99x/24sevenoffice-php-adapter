# 24sevenoffice-php-adapter
A PHP Adapter to use in integrations with https://24sevenoffice.com/ CRM platform

## Getting Started

Use the config.php to set the 24sevenoffice credentials http://developer.24sevenoffice.com/dev/#api-intro,
```php
      $username = "USER";  //Change this to your client user or community login
      $password = "PASS";  //Change this to your password
      $applicationid = "APP_ID";  //Change this to your applicationId
```

## Functions

### Save Companies
To save a company to the CRM you can use the following method and add the needed fields,

```php
        $company["companies"][0]['Name'] ="NAME";
        $company["companies"][0]['Type'] ="Business";
        $company["companies"][0]['OrganizationNumber'] ="ORG_ID";
```

For more data fields http://developer.24sevenoffice.com/diverse/apicompanyservice-datatypes/


### TODO
