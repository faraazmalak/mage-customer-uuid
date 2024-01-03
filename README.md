# Magento 2 Customer UUID Attribute Extension

## Objective

This Magento 2 extension introduces a new read-only attribute, `uuid`, for customers. The extension ensures the uniqueness of the UUID for each customer and automatically assigns it to existing and new customers.
The `uuid` attribute is exposed through a public GraphQl API for authenticated users and is also be displayed in the customer grid.

## Key features and implementation details

### 1. UUID implementation

- The extension uses UUID version 4, in accordance with Magento's best practices. UUID version 4 provides a very high probability of uniqueness due to its reliance on random values.
- Before any customer (new or existing) is assigned a UUID, the extension ensures that the UUID is not assinged to any other customer.
- To enforce data integrity, the extension always validates the assigned UUID, whenever changes are made to an existing customer record, either from admin panel or storefront. If the assigned UUID is invalid, a new one is generated, validated and assigned. When this happens, a UI notification is displayed on both admin panel and storefront.
- The extension uses Magento 2's plugin architecture, to intercept a customer save operation. During this interception, new UUIDs are assigned and existing ones are validated.

### 2. Accessing UUID via Customer Grid
The UUIDs are filterable, searchable and sortable in the customer grid.
![](C:\Users\FaraazMalak\Desktop\customer-grid.jpg)

### 3. Accessing UUID via GraphQl
For authenticated users, UUID is also accessible via GraphQL API.
Magento's GraphQl endpoint is `/graphql` and can be accessed at `http://<your-domain.com>/graphql`. It is recommended to use a GraphQl client like Postman to access this endpoint.

First, a bearer toekn must be obtained by calling `GenerateCustomerToken` mutation.
Example:
```
mutation GenerateCustomerToken {
    generateCustomerToken(email: "yourname@yourdomain.com", password: "yourpass") {
    token
    }
}

```

Next, this token can be passed along with a graphQL 

### 4. Visibility in Customer Grid

- Display the `uuid` attribute in the customer grid for easy reference.

### 5. Read-Only Attribute

- Make the `uuid` attribute read-only, preventing editing through the Magento 2 admin panel.

### 6. Automatic Assignment

- Upon module installation, assign a unique `uuid` to all existing customers.

### 7. Automatic Assignment for New Customers

- When a new customer is created, automatically generate and assign a unique `uuid`.

### 8. Composer Installation

- Implement the module as a Magento 2 extension, installable via `composer`.

### 9. Bonus: Testing

- Include comprehensive tests to ensure the functionality and integrity of the module.

## Technical Implementation

- Utilize Magento 2 best practices for extension development.
- Implement GraphQL queries and resolvers for the `uuid` attribute.
- Enforce attribute uniqueness through proper data validation.
- Configure the customer grid to display the `uuid` attribute.
- Set up read-only permissions for the `uuid` attribute.
- Implement hooks for automatic assignment during module installation and customer creation.

## Deliverables

- A Magento 2 extension package installable via `composer`.
- Documentation detailing installation steps, API access, and testing procedures.
- Bonus: Unit and integration tests ensuring the correctness of the implemented features.

## Installation

1. Install the module using composer:

   ```bash
   composer require your-vendor/module-uuid-attribute
   ```

2. Run Magento setup upgrade:

   ```bash
   bin/magento setup:upgrade
   ```

3. Clear the cache:

   ```bash
   bin/magento cache:clean
   ```

## API Access

- The `uuid` attribute can be accessed via GraphQL on the Customer object.

## Testing

- To run tests, execute:

   ```bash
   vendor/bin/phpunit
   ```

## Contribution

Contributions are welcome! Please follow Magento 2 coding standards and create a pull request.

## License

This Magento 2 extension is open-source and released under the [MIT License](LICENSE).
