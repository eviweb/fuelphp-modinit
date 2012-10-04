Fuel PHP Modinit Package
========================

This package adds some features to initialize modules automatically.    

Three ways to make a module compliant; one does not exclude the others. 
       
######_Case 1:_    
1.   create at the root of the module directory a file named ``bootstrap.php``    
2.   place initialization code in it   
    
######_Case 2:_    
1.   create at the root of the module directory a file named as the module,   
2.   add the module namespace as described in the [manual](http://docs.fuelphp.com/general/modules.html 
"Fuel PHP Modules Documentation"),   
3.   create a function named ``__init``   
4.   place initialization code in it   
    
######_Case 3:_    
1.   create in the _./classes_ directory a class named as as the module,    
2.   add the module namespace as described in the [manual](http://docs.fuelphp.com/general/modules.html 
"Fuel PHP Modules Documentation"),    
3.   create a static method named ``_init`` as described in the [manual](http://docs.fuelphp.com/general/classes.html 
"Fuel PHP Classes Documentation")    
4.   place initialization code in it  

To illustrate cases 2 and 3, here follow two examples :    
> Assumed the module is named _mymodule_, its namespace will automatically be __\\Mymodule__
>   
> ######_Case 2:_   
> create a file named ``./mymodule.php``
> add the code :    
>>      namespace Mymodule;    
>>      function __init()
>>      {
>>          // place your initilization code here
>>      }    
>    
> ######_Case 3:_   
> create a file named ``./classes/mymodule.php``
> add the code :    
>>      namespace Mymodule;    
>>      class Mymodule
>>      {
>>          public static function _init()
>>          {
>>              // place your initilization code here
>>          }
>>      }    
>  
   
__These examples assume that the module is referenced in the ``always_load.modules`` option of the application config file.__    
To inizialize a module loaded manually, call this package method ``Initializer::init()`` with the name of the module as argument, like this :   
>       \evidev\fuelphp\modinit\Initializer::init('mymodule');