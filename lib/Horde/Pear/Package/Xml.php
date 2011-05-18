<?php
/**
 * Handles package.xml files.
 *
 * PHP version 5
 *
 * @category Horde
 * @package  Pear
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Pear
 */

/**
 * Handles package.xml files.
 *
 * Copyright 2011 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @category Horde
 * @package  Pear
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Pear
 */
class Horde_Pear_Package_Xml
{
    /** The package.xml namespace */
    const XMLNAMESPACE = 'http://pear.php.net/dtd/package-2.0';

    /**
     * The parsed XML.
     *
     * @var DOMDocument
     */
    private $_xml;

    /**
     * The path to the XML file.
     *
     * @var string
     */
    private $_path;

    /**
     * The XPath query handler.
     *
     * @var DOMXpath
     */
    private $_xpath;

    /**
     * The factory for required instances.
     *
     * @var Horde_Pear_Package_Xml_Factory
     */
    private $_factory;

    /**
     * Constructor.
     *
     * @param resource|string $xml The package.xml as stream or path.
     */
    public function __construct($xml, $factory = null)
    {
        if (is_resource($xml)) {
            rewind($xml);
        } else {
            $this->_path = $xml;
            $xml = fopen($xml, 'r');
        }
        $this->_xml = new DOMDocument('1.0', 'UTF-8');
        $this->_xml->loadXML(stream_get_contents($xml));
        $this->_xpath = new DOMXpath($this->_xml);
        $this->_xpath->registerNamespace('p', self::XMLNAMESPACE);
        if ($factory === null) {
            $this->_factory = new Horde_Pear_Package_Xml_Factory();
        } else {
            $this->_factory = $factory;
        }
    }

    /**
     * Return the path to the package.xml file.
     *
     * @return string The path to the package.xml.
     */
    public function getContent($type = 'horde', $path = null)
    {
        if ($path === null) {
            if ($this->_path === null) {
                throw new Horde_Pear_Exception('The path has not been provided!');
            }
            $path = $this->_path;
        }
        if (!is_object($type)) {
            if ($type == 'horde' || !class_exists($type)) {
                $type = 'Horde_Pear_Package_Type_' . ucfirst($type);
            }
            $type = new $type(dirname($this->_path));
        }
        return new Horde_Pear_Package_Contents_List($type);
    }

    /**
     * Return the package name.
     *
     * @return string The name of the package.
     */
    public function getName()
    {
        return $this->getNodeText('/p:package/p:name');
    }

    /**
     * Return the license information.
     *
     * @return array The license information as array with the license name
     *               having the key "name" and the license URI having the key
     *               "uri".
     */
    public function getLicense()
    {
        return array(
            'name' => $this->getNodeText('/p:package/p:license'),
            'uri' => $this->findNode('/p:package')->getElementsByTagNameNS(self::XMLNAMESPACE, 'license')->item(0)->getAttribute('uri')
        );
    }

    /**
     * Catch undefined method calls and try to run them as task.
     *
     * @param string $name      The method/task name.
     * @param array  $arguments The arguments for the call.
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 6) == 'create') {
            return $this->_factory->create(substr($name, 6), $arguments);
        } else {
            array_unshift($arguments, $this);
            $this->_factory->createTask($name, $arguments)->run();
        }
    }

    /**
     * Mark the package as being release and set the timestamps to now.
     *
     * @return NULL
     */
    public function timestamp()
    {
        $this->replaceTextNode('/p:package/p:date', date('Y-m-d'));
        $this->replaceTextNode('/p:package/p:time', date('H:i:s'));

        $release = $this->_requireCurrentRelease();

        $this->replaceTextNodeRelativeTo('./p:date', $release, date('Y-m-d'));
    }

    /**
     * Synchronizes the current version information with the release information
     * in the changelog.
     *
     * @return NULL
     */
    public function syncCurrentVersion()
    {
        $date = $this->getNodeText('/p:package/p:date');
        $license = $this->getLicense();
        $notes = $this->getNodeText('/p:package/p:notes');
        $api = $this->getNodeText('/p:package/p:version/p:api');
        $stability_api = $this->getNodeText('/p:package/p:stability/p:api');
        $stability_release = $this->getNodeText(
            '/p:package/p:stability/p:release'
        );

        $release = $this->_requireCurrentRelease();

        $this->replaceTextNodeRelativeTo('./p:date', $release, $date);
        $this->replaceTextNodeRelativeTo(
            './p:notes', $release, $notes . '  '
        );
        $this->replaceTextNodeRelativeTo(
            './p:license',
            $release,
            $license['name'],
            array('uri' => $license['uri'])
        );
        $version_node = $this->findNodeRelativeTo(
            './p:version', $release
        );
        $this->replaceTextNodeRelativeTo(
            './p:api', $version_node, $api
        );
        $stability_node = $this->findNodeRelativeTo(
            './p:stability', $release
        );
        $this->replaceTextNodeRelativeTo(
            './p:api', $stability_node, $stability_api
        );
        $this->replaceTextNodeRelativeTo(
            './p:release', $stability_node, $stability_release
        );
    }

    /**
     * Add a new note to the package.xml
     *
     * @param string $note The note text.
     *
     * @return NULL
     */
    public function addNote($note)
    {
        $notes = trim($this->getNodeText('/p:package/p:notes'));
        if ($notes != '*') {
            $new_notes = "\n* " . $note . "\n" . $notes . "\n ";
        } else {
            $new_notes = "\n* " . $note . "\n ";
        }
        $this->replaceTextNode('/p:package/p:notes', $new_notes);

        $release = $this->_fetchCurrentRelease();
        if ($release !== null) {
            $this->replaceTextNodeRelativeTo(
                './p:notes', $release, $new_notes . '  '
            );
        }
    }

    /**
     * Add the next version to the package.xml
     *
     * @param string $version           The new version number.
     * @param string $initial_note      The text for the initial note.
     * @param string $stability_api     The API stability for the next release.
     * @param string $stability_release The stability for the next release.
     *
     * @return NULL
     */
    public function addNextVersion(
        $version,
        $initial_note,
        $stability_api = null,
        $stability_release = null
    ) {
        $notes = "\n* " . $initial_note . "\n ";
        $license = $this->getLicense();
        $api = $this->getNodeText('/p:package/p:version/p:api');
        if ($stability_api === null) {
            $stability_api = $this->getNodeText('/p:package/p:stability/p:api');
        }
        if ($stability_release === null) {
            $stability_release = $this->getNodeText(
                '/p:package/p:stability/p:release'
            );
        }
        $version_node = $this->findNode('/p:package/p:version');
        $this->replaceTextNodeRelativeTo(
            './p:release', $version_node, $version
        );
        $this->replaceTextNode('/p:package/p:notes', $notes);
        $this->replaceTextNode('/p:package/p:date', date('Y-m-d'));
        $this->replaceTextNode('/p:package/p:time', date('H:i:s'));

        $changelog = $this->findNode('/p:package/p:changelog');
        $this->_insertWhiteSpace($changelog, ' ');

        $release = $this->_xml->createElementNS(self::XMLNAMESPACE, 'release');
        $this->_appendVersion($release, $version, $api, "\n   ");
        $this->_appendStability($release, $stability_release, $stability_api, "\n   ");
        $this->_appendChild($release, 'date', date('Y-m-d'), "\n   ");
        $this->_appendLicense($release, $license, "\n   ");
        $this->_appendChild($release, 'notes', $notes . '  ', "\n   ");
        $this->_insertWhiteSpace($release, "\n  ");
        $changelog->appendChild($release);
        $this->_insertWhiteSpace($changelog, "\n ");
    }

    /**
     * Append version information.
     *
     * @param DOMNode $parent  The parent DOMNode.
     * @param string  $version The version.
     * @param string  $api     The api version.
     * @param string  $ws      Additional white space that should be inserted.
     *
     * @return NULL
     */
    private function _appendVersion($parent, $version, $api, $ws = '')
    {
        $this->_insertWhiteSpace($parent, $ws);
        $node = $this->_xml->createElementNS(self::XMLNAMESPACE, 'version');
        $this->_appendChild($node, 'release', $version, "\n    ");
        $this->_appendChild($node, 'api', $api, "\n    ");
        $parent->appendChild($node);
    }

    /**
     * Append stability information.
     *
     * @param DOMNode $parent  The parent DOMNode.
     * @param string  $release The release stability.
     * @param string  $api     The api stability.
     * @param string  $ws      Additional white space that should be inserted.
     *
     * @return NULL
     */
    private function _appendStability($parent, $release, $api, $ws = null)
    {
        $this->_insertWhiteSpace($parent, $ws);
        $node = $this->_xml->createElementNS(self::XMLNAMESPACE, 'stability');
        $this->_appendChild($node, 'release', $release, "\n    ");
        $this->_appendChild($node, 'api', $api, "\n    ");
        $parent->appendChild($node);
    }

    /**
     * Append license information.
     *
     * @param DOMNode $parent  The parent DOMNode.
     * @param array   $license The license information.
     * @param string  $ws      Additional white space that should be inserted.
     *
     * @return NULL
     */
    private function _appendLicense($parent, $license, $ws = null)
    {
        $this->_insertWhiteSpace($parent, $ws);
        $new_node = $this->_xml->createElementNS(
            self::XMLNAMESPACE, 'license'
        );
        $text = $this->_xml->createTextNode($license['name']);
        $new_node->appendChild($text);
        $new_node->setAttribute('uri', $license['uri']);
        $parent->appendChild($new_node);
    }

    /**
     * Fetch the node holding the current release information in the changelog
     * and fail if there is no such node.
     *
     * @return DOMElement|NULL The release node.
     *
     * @throws Horde_Pear_Exception If the node does not exist.
     */
    private function _requireCurrentRelease()
    {
        $release = $this->_fetchCurrentRelease();
        if ($release === null) {
            throw new Horde_Pear_Exception('No release in the changelog matches the current version!');
        }
        return $release;
    }

    /**
     * Fetch the node holding the current release information in the changelog.
     *
     * @return DOMElement|NULL The release node or empty if no such node was found.
     */
    private function _fetchCurrentRelease()
    {
        $version = $this->getNodeText('/p:package/p:version/p:release');
        foreach($this->findNodes('/p:package/p:changelog/p:release') as $release) {
            if ($this->getNodeTextRelativeTo('./p:version/p:release', $release) == $version) {
                return $release;
            }
        }
    }

    /**
     * Return the complete package.xml as string.
     *
     * @return string The package.xml content.
     */
    public function __toString()
    {
        $result = $this->_xml->saveXML();
        $result = preg_replace(
            '#<package (.*) (packagerversion="[.0-9]*" version="2.0")#',
            '<package \2 \1',
            $result
        );
        $result = preg_replace("#'#", '&apos;', $result);
        return preg_replace('#"/>#', '" />', $result);
    }

    /**
     * Return a single named node matching the given XPath query.
     *
     * @param string $query The query.
     *
     * @return DOMNode|false The named DOMNode or empty if no node was found.
     */
    public function findNode($query)
    {
        return $this->_findSingleNode($this->findNodes($query));
    }

    /**
     * Return a single named node below the given context matching the given
     * XPath query.
     *
     * @param string  $query   The query.
     * @param DOMNode $context Search below this node.
     *
     * @return DOMNode|false The named DOMNode or empty if no node was found.
     */
    public function findNodeRelativeTo($query, DOMNode $context)
    {
        return $this->_findSingleNode(
            $this->findNodesRelativeTo($query, $context)
        );
    }

    /**
     * Return a single node for the result set.
     *
     * @param DOMNodeList $result The query result.
     *
     * @return DOMNode|false The DOMNode or empty if no node was found.
     */
    private function _findSingleNode($result)
    {
        if ($result->length) {
            return $result->item(0);
        }
        return false;
    }

    /**
     * Return all nodes matching the given XPath query.
     *
     * @param string $query The query.
     *
     * @return DOMNodeList The list of DOMNodes.
     */
    public function findNodes($query)
    {
        return $this->_xpath->query($query);
    }

    /**
     * Return all nodes matching the given XPath query.
     *
     * @param string $query The query.
     *
     * @return DOMNodeList The list of DOMNodes.
     */
    public function findNodesRelativeTo($query, $context)
    {
        return $this->_xpath->query($query, $context);
    }

    /**
     * Return the content of a single named node matching the given XPath query.
     *
     * @param string $path The node path.
     *
     * @return string|false The node content as string or empty if no node was
     *                      found.
     */
    public function getNodeText($path)
    {
        if ($node = $this->findNode($path)) {
            return $node->textContent;
        }
        throw new Horde_Pear_Exception(
            sprintf('"%s" element is missing!', $path)
        );
    }

    /**
     * Return the content of a single named node below the given context
     * and matching the given XPath query.
     *
     * @param string  $path    The node path.
     * @param DOMNode $context Search below this node.
     *
     * @return string|false The node content as string or empty if no node was
     *                      found.
     */
    public function getNodeTextRelativeTo($path, DOMNode $context)
    {
        if ($node = $this->findNodeRelativeTo($path, $context)) {
            return $node->textContent;
        }
        throw new Horde_Pear_Exception(
            sprintf('"%s" element is missing!', $path)
        );
    }

    /**
     * Replace a specific text node
     *
     * @param string $path  The XPath query pointing to the node.
     * @param string $value The new text value.
     *
     * @return DOMNodeList The list of DOMNodes.
     */
    public function replaceTextNode($path, $value)
    {
        if ($node = $this->findNode($path)) {
            $this->_xml->documentElement->replaceChild(
                $this->_replacementNode($node, $value), $node
            );
        }
    }

    /**
     * Replace a specific text node
     *
     * @param string  $path      The XPath query pointing to the node.
     * @param DOMNode $context   Search below this node.
     * @param string  $value     The new text value.
     * @param array   $attribues Attributes to add to the node.
     *
     * @return DOMNodeList The list of DOMNodes.
     */
    public function replaceTextNodeRelativeTo(
        $path,
        DOMNode $context,
        $value,
        $attributes = array()
    ) {
        if ($node = $this->findNodeRelativeTo($path, $context)) {
            $new_node = $this->_replacementNode($node, $value);
            foreach ($attributes as $name => $value) {
                $new_node->setAttribute($name, $value);
            }
            $context->replaceChild($new_node, $node);
        }
    }

    /**
     * Generate a replacement node.
     *
     * @param DOMNode $old_node The old DOMNode to be replaced.
     * @param string  $value    The new text value.
     *
     * @return DOMNode The new DOMNode.
     */
    private function _replacementNode($old_node, $value)
    {
        $new_node = $this->_xml->createElementNS(
            self::XMLNAMESPACE, $old_node->tagName
        );
        $text = $this->_xml->createTextNode($value);
        $new_node->appendChild($text);
        return $new_node;
    }

    /**
     * Append a new child.
     *
     * @param DOMNode $parent The parent DOMNode.
     * @param string  $name   The tag name of the new node.
     * @param string  $value  The text content of the new node.
     * @param string  $ws     Additional white space that should be inserted.
     *
     * @return NULL
     */
    private function _appendChild($parent, $name, $value, $ws = '')
    {
        $this->_insertWhiteSpace($parent, $ws);
        $new_node = $this->_xml->createElementNS(
            self::XMLNAMESPACE, $name
        );
        $text = $this->_xml->createTextNode($value);
        $new_node->appendChild($text);
        $parent->appendChild($new_node);
    }

    /**
     * Insert some white space.
     *
     * @param DOMNode $parent The parent DOMNode.
     * @param string  $ws     Additional white space that should be inserted.
     *
     * @return DOMNode The inserted white space node.
     */
    public function _insertWhiteSpace($parent, $ws)
    {
        $ws_node = $this->_xml->createTextNode($ws);
        $parent->appendChild($ws_node);
        return $ws_node;
    }


    public function insert($elements, $point)
    {
        if (!is_array($elements)) {
            $elements = array($elements);
        }
        $node = null;
        foreach ($elements as $key => $element) {
            if (is_string($element)) {
                $element = $this->createText($element);
            } else if (is_array($element)) {
                $node = $element = $this->createNode($key, $element);
            }
            $point->parentNode->insertBefore($element, $point);
        }
        return $node;
    }

    public function append($elements, $parent)
    {
        if (!is_array($elements)) {
            $elements = array($elements);
        }
        $node = null;
        foreach ($elements as $key => $element) {
            if (is_string($element)) {
                $element = $this->createText($element);
            } else if (is_array($element)) {
                $node = $element = $this->createNode($key, $element);
            }
            $parent->appendChild($element);
        }
        return $node;
    }

    public function createText($text)
    {
        return $this->_xml->createTextNode($text);
    }

    public function createComment($comment)
    {
        return $this->_xml->createComment($comment);
    }

    public function createNode($name, $attributes = array())
    {
        $node = $this->_xml->createElementNS(self::XMLNAMESPACE, $name);
        foreach ($attributes as $key => $value) {
            $node->setAttribute($key, $value);
        }
        return $node;
    }

    public function removeWhitespace($node)
    {
        if ($node) {
            $ws = trim($node->textContent);
            if (empty($ws)) {
                $node->parentNode->removeChild($node);
            }
        }
    }

    public function removeComment($node, $comment)
    {
        if ($node) {
            $current = trim($node->textContent);
            if ($current == $comment) {
                $node->parentNode->removeChild($node);
            }
        }
    }
}