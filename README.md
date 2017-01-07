# Multitenancy

A helpful library for multitenancy support in Symfony applications.

What it does:

* Determine the tenant from a route parameter.
* Scope ORM queries to the current tenant.
* Scope new entities to the current tenant.
* Sets the default route parameter.
* Controller argument resolver for the tenant.

## Installation

Use Composer to install the package:

```
composer require keystone/multitenancy
```

Add the Symfony bundle to your kernel and configure it:

```php
public function registerBundles()
{
    $bundles[] = new Keystone\Multitenancy\Bundle\KeystoneMultitenancyBundle();
}
```

```yml
keystone_multitenancy:
  tenant_repository_id: app.repository.tenant
  tenant_route_parameter: tenantSubdomain
  tenant_filter_column: tenant_id
```
