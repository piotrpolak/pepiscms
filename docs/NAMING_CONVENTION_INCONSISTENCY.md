# Naming convention inconsistency

There is a naming convention inconsistency among PepisCMS and CodeIgniter.

This inconsistency will not be changed in order to keep backward compatibility.

* CodeIgniter uses `snake_style` for both method names and variable names
* PepisCMS user `camelCase` for method names and variables to hold objects
* PepisCMS uses `snake_style` for primitive variables, arrays and some of the callback methods

Please note that PepisCMS was initially started in 2007 (11 years ago).
Throughout this time I tried to keep the number of the breaking changes to minimum.
