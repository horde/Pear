==============
 Introduction
==============

The Horde_Pear_Remote class provides you with high-level access to the REST interface of a PEAR server.

Creating an instance of this class without providing any arguments to the constructor will allow to access the PEAR server at `pear.horde.org`_.

.. _`pear.horde.org`: https://pear.horde.org

::

 $pear = new Horde_Pear_Remote();
 print(join("\n", $pear->listPackages()));
 
 Horde_ActiveSync
 Horde_Alarm
 Horde_Argv
 Horde_Auth
 Horde_Autoloader
 ...

This can be easily modified by specifying an alternate server name as a first argument:

::

 $pear = new Horde_Pear_Remote('pear.phpunit.de');
 print(join("\n", $pear->listPackages()));
 
 DbUnit
 File_Iterator
 Object_Freezer
 PHPUnit
 ...

==============
 API overview
==============

The following provides a generic overview of the API provided by Horde_Pear_Remote. A detailed version based on the information extracted from the code can be found `here`_.

.. _`here`: http://dev.horde.org/api/framework/Pear/

--------------
 getChannel()
--------------

Returns the channel.xml for the server as string.

::

 print($pear->getChannel());
 
 <?xml version="1.0" encoding="UTF-8" ?>
 <channel version="1.0" xmlns="http://pear.php.net/channel-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pear.php.net/channel-1.0 http://pear.php.net/dtd/channel-1.0.xsd">
     <name>pear.horde.org</name>
     <summary>Horde PEAR server</summary>
     <suggestedalias>horde</suggestedalias>
     <servers>
         <primary>
             <rest>
                 <baseurl type="REST1.0">https://pear.horde.org/rest/</baseurl>
                 <baseurl type="REST1.1">https://pear.horde.org/rest/</baseurl>
                 <baseurl type="REST1.2">https://pear.horde.org/rest/</baseurl>
                 <baseurl type="REST1.3">https://pear.horde.org/rest/</baseurl>
             </rest>
         </primary>
     </servers>
 </channel>

----------------
 listPackages()
----------------

This returns an array with the list of package names. Use this to get a quick overview on what is available on the remote server.

::

 print(join("\n", $pear->listPackages()));
 
 Horde_ActiveSync
 Horde_Alarm
 Horde_Argv
 Horde_Auth
 Horde_Autoloader
 ...

--------------------
 getLatestRelease()
--------------------

For a given package name this will retrieve the latest version that has been released. By default the method only selects stable releases. The optional second parameter allows to modify this behaviour to specifically return the highest release version of the specified stability. If the stability does not matter the argument can be set to NULL to retrieve the highest release version independent of the stability.

::

 print($pear->getLatestRelease('Horde_Core'));
 
 1.7.0

------------------------
 getLatestDownloadUri()
------------------------

This will deliver the download location for the source archive of the latest version that has been released for the specified package.

The "stability" parameter works in the same way as for the getLatestRelease() method above.

::

 print($pear->getLatestDownloadUri('Horde_Core'));
 
 https://pear.horde.org/get/Horde_Core-1.7.0.tgz

--------------------
 getLatestDetails()
--------------------

This will deliver detailed information for the latest release of the specified package.

The "stability" parameter works in the same way as for the getLatestRelease() method above.

::

 print_r($pear->getLatestDetails('Horde_Core'));
 
 Horde_Pear_Rest_Release Object                                      
 (                                                                                                 
     [_element:protected] => DOMElement Object
         (
         )
 
     [_serialized:protected] =>
     [_parentElement:protected] =>
     [_children:protected] =>
     [_appended:protected] => 1
 )

-----------------
 releaseExists()
-----------------

Checks if a release exists for the specified combination of package name and version number.

::

 print($pear->releaseExists('Horde_Core', '1.7.0'));
 
 1

-------------------
 getDependencies()
-------------------

Returns the dependencies for the specified package version. The return value is an array ...?

::

 print(count($pear->getDependencies('Horde_Exception', '1.0.0')));
 
 4

-----------------
 getPackageXml()
-----------------

Returns the package.xml file wrapped as Horde_Pear_Package_Xml instance.

::

 print($pear->getPackageXml('Horde_Exception', '1.0.0')->getName());
 
 Horde_Exception

