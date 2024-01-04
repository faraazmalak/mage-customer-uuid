# Magento 2 Customer UUID Attribute Extension

## Objective

This Magento 2 extension introduces a new read-only attribute, `uuid`, for customers. The extension ensures the uniqueness of the UUID for each customer and automatically assigns it to existing and new customers. The `uuid` attribute is exposed through a public GraphQl API for authenticated users and is also be displayed in the customer grid.

## Key Features and Implementation Details
1. The extension uses UUID version 4, in accordance with Magento's best practices. UUID version 4 provides a very high probability of uniqueness due to its reliance on random values.
2. The extension uses Magento 2's plugin architecture, to intercept customer save and update operations. During this interception, new UUIDs are assigned and existing ones are re-validated.
4. Upon installation, the extension auto-assigns UUIDs to all the existing customers. This is implemented using Magento's data patches. 
4. After the extension is installed, all new customers created thereafter are auto-assigned a UUID, just before the customer record is committed to the database. 
5. Before any customer (new or existing) is assigned a UUID, the extension ensures that the same UUID is not assinged to another customer.
6. To enforce data integrity, the extension always re-validates the existing UUID for a customer, whenever changes are made to the customer record, either from admin panel or storefront. If the assigned UUID is invalid, a new one is generated, validated and assigned. A UI notification is also displayed on both admin panel and storefront.
8. UUIDs are displayed on customer grid, as read-only and are filterable, searchable and sortable.
9. An authenticated user can read the UUID through the GraphQl API
10. Extension logs all the UUID transactions to a log file.

## Magento Compatability
This extension has been developed and tested on ``Magento 2 version 2.4.6-p2``, with no other third-party extensions installed.

## Setting-up Testing Environment
This [docker-compose.yml](https://raw.githubusercontent.com/faraazmalak/magento_docker/main/docker-compose.yml) can be used to quickly spin-up a local testing environment, with Magento 2.4.6-p2, mariadb and elastic search installed. Once the ``docker-compose.yml`` file is downloaded,  ``docker-compose up -d`` can be used to download the docker images and setup the containers and volumes. 


## Recommended Steps Before Extension Installation
1. Ensure that Magento installation has a few customers created. This will allow the extension to auto-assign UUID during installation. 
2. The extension logs all UUID transations to ``<magento_root>/var/log/quarry_customeruuid.log``. Please ensure this log file has write-permissions enabled. If not, the extension falls back to PHP's system logger.
3. Enable Magento's developer mode
   ```bash
   bin/magento deploy:mode:set developer
   ```
4. Disable Magento's cache
   ```bash
   bin/magento cache:disable
   ```

## Extension Installation Process
1. Install the extension using composer:
   ```bash
   composer require quarry/customer-uuid
   ```
2. Flush Magento's cache
   ```bash
   bin/magento cache:flush
   ```
3. Clean Magento's cache
   ```bash
   bin/magento cache:clean
   ```
4. Enable the UUID extension
   ```bash
   bin/magento module:enable Quarry_CustomerUuid
   ```
5. Run Magento setup upgrade:
   ```bash
   bin/magento setup:upgrade
   ```
6. Run di compile:
   ```bash
   bin/magento setup:di:compile
   ```
## Verify the Extension Installation
### 1. Existing customers are auto-assigned UUID during extension installation. 
Expected outcome:
1. Customer grid, should show a new column labelled ``UUID``, with UUIDs assigned to all the existing customers.
2. These read-only UUIDs must be filterable, searchable and sortable in the customer grid.
![customer-grid](https://github.com/faraazmalak/mage-customer-uuid/assets/3054432/15863948-86f0-452a-a332-a808e1b1e008)
3. Log file should show all the exsiting customer IDs, to whom new UUIDs have been assigned.
Log entry message should be in following format:
```
[2024-01-04T18:30:41.104753+00:00] logger.WARNING: UUID <uuid_code> changed for customer ID <customer_id>
```

### 2. New customers created from admin panel, are auto-assigned UUID
Expected outcome: 
1. The new customer record must show up in the customer grid, with a UUID assigned.
2. The log file should contain an entry for this transaction.
Log entry message should be in following format:
```
UUID <uuid_code> assigned to new customer
```

### 2. New customers created from storefront, are auto-assigned UUID
Expected outcome: 
1. The new customer record should show up in the admin customer grid, with a UUID assigned.
2. The log file should contain an entry for this transaction.
Log entry message should be in following format:
```
UUID <uuid_code> assigned to new customer
```

### 3. UUID is accessible through GraphQl API, for authenticated users.
For authenticated users, UUID is accessible via GraphQL API on the Customer object. Magento's GraphQl endpoint is `/graphql` and can be accessed at `http://<your-domain.com>/graphql`. It is recommended to use a GraphQl client like Postman to access this endpoint.

First, a bearer toekn must be obtained by calling `GenerateCustomerToken` mutation.
Example:
```
mutation GenerateCustomerToken {
    generateCustomerToken(email: "yourname@yourdomain.com", password: "yourpass") {
    token
    }
}

```
Next, this auth token must be passed along with the GraphQl query to retrieve UUID.
Sample GraphQl query to retrieve UUID:
```
query Customer {
    customer {
        uuid
    }
}

```
Below is a sample response to the above GraphQl query: 
```
{
    "data": {
        "customer": {
            "uuid": "8d56ce70-5ec1-4eaa-91f8-5cee9bb72d90"
        }
    }
}
```

## Extension Uninstallation Process
1. Disable the UUID extension
   ```bash
   bin/magento module:disable Quarry_CustomerUuid
   ```
2. Uninstall the UUID extension
   ```bash
   bin/magento module:uninstall --non-composer Quarry_CustomerUuid
   ```
3. Remove the extension using composer:
   ```bash
   composer remove quarry/customer-uuid
   ```
4. Flush Magento's cache
   ```bash
   bin/magento cache:flush
   ```
5. Clean Magento's cache
   ```bash
   bin/magento cache:clean
   ```
6. Run Magento setup upgrade:
   ```bash
   bin/magento setup:upgrade
   ```
7. Run di compile:
   ```bash
   bin/magento setup:di:compile
   ```

