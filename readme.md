#### Background / timing
- problem received & reviewed on Tue 
- implementation - 8:30am - 11:30am, final notes from 11:30 until 12:10pm 
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
- unzip cart.zip
- composer install from cart directory (for phpunit, etc)

#### Directory Structure
- src/Exception = namespaced exception instances
- src/Interfaces - interfaces for Cart, CartItem - could be brought to Laravel (or similar) for DB backed version
- src/Pipeline - pipeline & pipeline steps that are applied after every cart change
- src/Services - stub services for Catalog, Discount and Tax concepts
- src/* - domain models.  Address model is necessary stub.
- tests/* = unit tests proving requirements are met

#### Basic Architecture
A **Cart** consists of 0 or more **CartItems**.  Whenever the contents of a Cart is
modified (such as items being added, changed or removed or a change in destination) an
**Pipeline** is executed to update any derivative information such as item discounts
or tax rates (might also include shipping costs, gift wrap charges, additional types
of discounts such as coupons or order level promotions - the list can go own).  Each 
step of the Pipeline is applied in turn, resulting in a Cart in a valid and consistent
state. 
