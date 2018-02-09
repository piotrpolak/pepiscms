# Security policy

Security policy is a set of required user rights above certain entities required to access a controller's method.
Every single method has associated a minimal right above a certain entity.

All violations of security policy are reported using system logs.

When a security policy is created, the administrator first defines desired entities (such as `page`, `user`, `group`,
`configuration` etc.) and assigns the entities to methods of controllers.

A reflection mechanism is used to scan for the controller methods. 

Security policy can be changed at any time without need to change code or recompile anything. AdminControllers initializes SecurityManager that handles security transparently to the programmer or user of the system. 

Security policy is serialized onto XML file that is platform independent and can be manually edited. In order to reduce time needed for reparsing XML file, the security policy is storied in a compiled and serialized cached format on the permanent storage or directly in the memory. 

## Access levels

There are four access levels:

* `NONE`
* `READ`
* `WRITE`
* `ALL`


## Types of security policy

There are two types of security policy:

* System security policy
* Module security policy

## User groups 

Once the security policy is created for both core and modules the administrator can create user groups. Creating a group is process similar to building security policy - the administrator picks an access level for every single entity that is granted to anyone who belongs to the group. 
A single user can belong to any number of user groups. This offers high flexibility because you can manage the groups separately. 