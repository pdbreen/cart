#### GENERAL
- problem received & reviewed on Tue 
- implementation - 8:30am - 11:30am 
- PHP version 7.2 using PHP Storm
- focus on straight PHP, without framework or database
- makes use of Koriym PHP skeleton - https://github.com/koriym/Koriym.PhpSkeleton - for dev dependencies

#### Requirement Assumptions
These additional assumptions have been made.  In reality, these would have been discussed
w/product owners to ensure they are appropriate assumptions to make.

- products have a unique identifier (SKU)
- product quantities are whole numbers
- multiple additions of same SKU are coalesced in same line item in cart

#### Service Dependencies 
There are a handful of services (CatalogService, TaxService, DiscountService) that 
are required to verify in scope functionality works as desired, but the services 
themselves are not technically in scope.  

The service implementations included are sufficient to test base requirements, 
but are in no way suitable for an actual implementation (time box decision).

#### Installation
  
