# Magento 2 Customer UUID Attribute Extension

## Objective

This Magento 2 extension introduces a new read-only attribute, `uuid`, for customers. The extension ensures the uniqueness of the UUID for each customer and automatically assigns it to existing and new customers.
The `uuid` attribute is exposed through a public GraphQl API for authenticated users and is also be displayed in the customer grid.

## Key features and implementation details
1. The extension uses UUID version 4, in accordance with Magento's best practices. UUID version 4 provides a very high probability of uniqueness due to its reliance on random values.
2. The extension uses Magento 2's plugin architecture, to intercept customer save and update operations. During this interception, new UUIDs are assigned and existing ones are re-validated.
4. Upon installation, the extension auto-assigns UUIDs to all the existing customers. This is implemented using Magento's data patches. 
4. After the extension is installed, all new customers created thereafter are auto-assigned a UUID, just before the new customer record is committed to the database. 
5. Before any customer (new or existing) is auto-assigned a UUID, the extension ensures that the same UUID is not assinged to another customer.
6. To enforce data integrity, the extension always re-validates the existing UUID for a customer, whenever changes are made to the customer record, either from admin panel or storefront. If the assigned UUID is invalid, a new one is generated, validated and auto-assigned. A UI notification is also displayed on both admin panel and storefront.
7. Extension logs all the UUID transactions to a log file.

## Recommended steps before extension installation
1. Ensure that Magento installation has a few customers created. This will allow the extension to auto-assign UUID during installation. 
2. The extension logs all UUID transations at ``<magento_root>/var/log/quarry_customeruuid.log``. Please ensure this log file has write-permissions enabled. If not, the extension falls back to PHP's system logger.
3. Enable Magento's developer mode
   ```bash
   ```
4. Disable Magento's cache
   ```bash
   ```

## Extension installation
1. Install the module using composer:
   ```bash
   composer require quarry/customer-uuid
   ```
2. Flush Magento's cache
   ```bash
   ```
3. Clean Magento's cache
   ```bash
   ```
4. Enable the UUID extension
   ```bash
   ```
5. Run Magento setup upgrade:
   ```bash
   bin/magento setup:upgrade
   ```
6. Run di compile:
   ```bash
   ```
## Functionality specifications
### 1. Existing customers are auto-assigned UUID during extension installation. 
1. Customer grid, should show a new UUID column, with UUIDs assigned to all the existing customers.
2. These read-only UUIDs are filterable, searchable and sortable in the customer grid.
![customer-grid](https://github.com/faraazmalak/mage-customer-uuid/assets/3054432/15863948-86f0-452a-a332-a808e1b1e008)
3. Log file should show all the exsiting customers,  to whom new UUIDs have been assigned.
Sample log file output:

### 2. New customers created from admin panel, are auto-assigned UUID
Expected result: 
1. The new customer record should show up in the customer grid, with a UUID assigned.
2. The log file should contain an entry for this transaction.
Sample log file entry:

### 2. New customers created from storefront, are auto-assigned UUID
Expected result: 
1. The new customer record should show up in the customer grid, with a UUID assigned.
2. The log file should contain an entry for this transaction.
Sample log file entry:

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
Sample GraphQl query


Below is a sample response to the above GraphQl query: 
