<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.133
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Libs;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Registry implements \JsonSerializable, \ArrayAccess, \IteratorAggregate, \Countable
{
	/**
	 * Registry Object
	 *
	 * @var    \stdClass
	 * @since  1.0
	 */
	protected $data;

	/**
	 * Flag if the Registry data object has been initialized
	 *
	 * @var    boolean
	 * @since  1.5.2
	 */
	protected $initialized = false;

	/**
	 * Registry instances container.
	 *
	 * @var    Registry[]
	 * @since  1.0
	 * @deprecated  2.0  Object caching will no longer be supported
	 */
	protected static $instances = array();

	/**
	 * Path separator
	 *
	 * @var    string
	 * @since  1.4.0
	 */
	public $separator = '.';

	/**
	 * Constructor
	 *
	 * @param   mixed  $data  The data to bind to the new Registry object.
	 *
	 * @since   1.0
	 */
	public function __construct($data = null)
	{
		// Instantiate the internal data object.
		$this->data = new \stdClass;

		// Optionally load supplied data.
		if ($data instanceof Registry)
		{
			$this->merge($data);
		}
		elseif (is_array($data) || is_object($data))
		{
			$this->bindData($this->data, $data);
		}
		elseif (!empty($data) && is_string($data))
		{
			$this->loadString($data);
		}
	}

	/**
	 * Magic function to clone the registry object.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function __clone()
	{
		$this->data = unserialize(serialize($this->data));
	}

	/**
	 * Magic function to render this object as a string using default args of toString method.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Count elements of the data object
	 *
	 * @return  integer  The custom count as an integer.
	 *
	 * @link    https://secure.php.net/manual/en/countable.count.php
	 * @since   1.3.0
	 */
    #[\ReturnTypeWillChange]
	public function count()
	{
		return count(get_object_vars($this->data));
	}

	/**
	 * Implementation for the JsonSerializable interface.
	 * Allows us to pass Registry objects to json_encode.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @note    The interface is only present in PHP 5.4 and up.
	 */
    #[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return $this->data;
	}

	/**
	 * Sets a default value if not already assigned.
	 *
	 * @param   string  $key      The name of the parameter.
	 * @param   mixed   $default  An optional value for the parameter.
	 *
	 * @return  mixed  The value set, or the default if the value was not previously set (or null).
	 *
	 * @since   1.0
	 */
	public function def($key, $default = '')
	{
		$value = $this->get($key, $default);
		$this->set($key, $value);

		return $value;
	}

	/**
	 * Check if a registry path exists.
	 *
	 * @param   string  $path  Registry path
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function exists($path)
	{
		// Return default value if path is empty
		if (empty($path))
		{
			return false;
		}

		// Explode the registry path into an array
		$nodes = explode($this->separator, $path);

		// Initialize the current node to be the registry root.
		$node  = $this->data;
		$found = false;

		// Traverse the registry to find the correct node for the result.
		foreach ($nodes as $n)
		{
			if (is_array($node) && isset($node[$n]))
			{
				$node  = $node[$n];
				$found = true;
				continue;
			}

			if (!isset($node->$n))
			{
				return false;
			}

			$node  = $node->$n;
			$found = true;
		}

		return $found;
	}

	/**
	 * Get a registry value.
	 *
	 * @param   string  $path     Registry path
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 *
	 * @return  mixed  Value of entry or null
	 *
	 * @since   1.0
	 */
	public function get($path, $default = null)
	{
		// Return default value if path is empty
		if (empty($path))
		{
			return $default;
		}

		if (!strpos($path, $this->separator))
		{
			return (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '') ? $this->data->$path : $default;
		}

		// Explode the registry path into an array
		$nodes = explode($this->separator, trim($path));

		// Initialize the current node to be the registry root.
		$node  = $this->data;
		$found = false;

		// Traverse the registry to find the correct node for the result.
		foreach ($nodes as $n)
		{
			if (is_array($node) && isset($node[$n]))
			{
				$node  = $node[$n];
				$found = true;

				continue;
			}

			if (!isset($node->$n))
			{
				return $default;
			}

			$node  = $node->$n;
			$found = true;
		}

		if (!$found || $node === null || $node === '')
		{
			return $default;
		}

		return $node;
	}

	/**
	 * Returns a reference to a global Registry object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * <pre>$registry = Registry::getInstance($id);</pre>
	 *
	 * @param   string  $id  An ID for the registry instance
	 *
	 * @return  Registry  The Registry object.
	 *
	 * @since   1.0
	 * @deprecated  2.0  Instantiate a new Registry instance instead
	 */
	public static function getInstance($id)
	{
		if (empty(self::$instances[$id]))
		{
			self::$instances[$id] = new self;
		}

		return self::$instances[$id];
	}

	/**
	 * Gets this object represented as an ArrayIterator.
	 *
	 * This allows the data properties to be accessed via a foreach statement.
	 *
	 * @return  \ArrayIterator  This object represented as an ArrayIterator.
	 *
	 * @see     IteratorAggregate::getIterator()
	 * @since   1.3.0
	 */
    #[\ReturnTypeWillChange]
	public function getIterator()
	{
		return new \ArrayIterator($this->data);
	}

	/**
	 * Load an associative array of values into the default namespace
	 *
	 * @param   array    $array      Associative array of value to load
	 * @param   boolean  $flattened  Load from a one-dimensional array
	 * @param   string   $separator  The key separator
	 *
	 * @return  Registry  Return this object to support chaining.
	 *
	 * @since   1.0
	 */
	public function loadArray($array, $flattened = false, $separator = null)
	{
		if (!$flattened)
		{
			$this->bindData($this->data, $array);

			return $this;
		}

		foreach ($array as $k => $v)
		{
			$this->set($k, $v, $separator);
		}

		return $this;
	}

	/**
	 * Load the public variables of the object into the default namespace.
	 *
	 * @param   object  $object  The object holding the publics to load
	 *
	 * @return  Registry  Return this object to support chaining.
	 *
	 * @since   1.0
	 */
	public function loadObject($object)
	{
		$this->bindData($this->data, $object);

		return $this;
	}

	/**
	 * Load the contents of a file into the registry
	 *
	 * @param   string  $file     Path to file to load
	 * @param   string  $format   Format of the file [optional: defaults to JSON]
	 * @param   array   $options  Options used by the formatter
	 *
	 * @return  Registry  Return this object to support chaining.
	 *
	 * @since   1.0
	 */
	public function loadFile($file, $format = 'JSON', $options = array())
	{
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$data = file_get_contents($file);

		return $this->loadString($data, $format, $options);
	}

	/**
	 * Load a string into the registry
	 *
	 * @param   string  $data     String to load into the registry
	 * @param   string  $format   Format of the string
	 * @param   array   $options  Options used by the formatter
	 *
	 * @return  Registry  Return this object to support chaining.
	 *
	 * @since   1.0
	 */
	public function loadString($data, $format = 'JSON', $options = array())
	{
		// Load a string into the given namespace [or default namespace if not given]
		$handler = Vendors\AbstractRegistryFormat::getInstance($format, $options);

		$obj = $handler->stringToObject($data, $options);

		// If the data object has not yet been initialized, direct assign the object
		if (!$this->initialized)
		{
			$this->data        = $obj;
			$this->initialized = true;

			return $this;
		}

		$this->loadObject($obj);

		return $this;
	}

	/**
	 * Merge a Registry object into this one
	 *
	 * @param   Registry  $source     Source Registry object to merge.
	 * @param   boolean   $recursive  True to support recursive merge the children values.
	 *
	 * @return  Registry|false  Return this object to support chaining or false if $source is not an instance of Registry.
	 *
	 * @since   1.0
	 */
	public function merge($source, $recursive = false)
	{
		if (!$source instanceof Registry)
		{
			return false;
		}

		$this->bindData($this->data, $source->toArray(), $recursive, false);

		return $this;
	}

	/**
	 * Method to extract a sub-registry from path
	 *
	 * @param   string  $path  Registry path
	 *
	 * @return  Registry|null  Registry object if data is present
	 *
	 * @since   1.2.0
	 */
	public function extract($path)
	{
		$data = $this->get($path);

		if ($data === null)
		{
			return null;
		}

		return new Registry($data);
	}

	/**
	 * Checks whether an offset exists in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 *
	 * @return  boolean  True if the offset exists, false otherwise.
	 *
	 * @since   1.0
	 */
    #[\ReturnTypeWillChange]
	public function offsetExists($offset)
	{
		return (boolean) ($this->get($offset) !== null);
	}

	/**
	 * Gets an offset in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 *
	 * @return  mixed  The array value if it exists, null otherwise.
	 *
	 * @since   1.0
	 */
    #[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	/**
	 * Sets an offset in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 * @param   mixed  $value   The array value.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
    #[\ReturnTypeWillChange]
	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	/**
	 * Unsets an offset in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
    #[\ReturnTypeWillChange]
	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}

	/**
	 * Set a registry value.
	 *
	 * @param   string  $path       Registry Path
	 * @param   mixed   $value      Value of entry
	 * @param   string  $separator  The key separator
	 *
	 * @return  mixed  The value of the that has been set.
	 *
	 * @since   1.0
	 */
	public function set($path, $value, $separator = null)
	{
		if (empty($separator))
		{
			$separator = $this->separator;
		}

		/*
		 * Explode the registry path into an array and remove empty
		 * nodes that occur as a result of a double separator. ex: wp..test
		 * Finally, re-key the array so they are sequential.
		 */
		$nodes = array_values(array_filter(explode($separator, $path), 'strlen'));

		if (!$nodes)
		{
			return null;
		}

		// Initialize the current node to be the registry root.
		$node = $this->data;

		// Traverse the registry to find the correct node for the result.
		for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++)
		{
			if (is_object($node))
			{
				if (!isset($node->{$nodes[$i]}) && ($i !== $n))
				{
					$node->{$nodes[$i]} = new \stdClass;
				}

				// Pass the child as pointer in case it is an object
				$node = &$node->{$nodes[$i]};

				continue;
			}

			if (is_array($node))
			{
				if (($i !== $n) && !isset($node[$nodes[$i]]))
				{
					$node[$nodes[$i]] = new \stdClass;
				}

				// Pass the child as pointer in case it is an array
				$node = &$node[$nodes[$i]];
			}
		}

		// Get the old value if exists so we can return it
		switch (true)
		{
			case (is_object($node)):
				$result = $node->{$nodes[$i]} = $value;
				break;

			case (is_array($node)):
				$result = $node[$nodes[$i]] = $value;
				break;

			default:
				$result = null;
				break;
		}

		return $result;
	}

	/**
	 * Append value to a path in registry
	 *
	 * @param   string  $path   Parent registry Path
	 * @param   mixed   $value  Value of entry
	 *
	 * @return  mixed  The value of the that has been set.
	 *
	 * @since   1.4.0
	 */
	public function append($path, $value)
	{
		$result = null;

		/*
		 * Explode the registry path into an array and remove empty
		 * nodes that occur as a result of a double dot. ex: wp..test
		 * Finally, re-key the array so they are sequential.
		 */
		$nodes = array_values(array_filter(explode('.', $path), 'strlen'));

		if ($nodes)
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			// TODO Create a new private method from part of code below, as it is almost equal to 'set' method
			for ($i = 0, $n = count($nodes) - 1; $i <= $n; $i++)
			{
				if (is_object($node))
				{
					if (!isset($node->{$nodes[$i]}) && ($i !== $n))
					{
						$node->{$nodes[$i]} = new \stdClass;
					}

					// Pass the child as pointer in case it is an array
					$node = &$node->{$nodes[$i]};
				}
				elseif (is_array($node))
				{
					if (($i !== $n) && !isset($node[$nodes[$i]]))
					{
						$node[$nodes[$i]] = new \stdClass;
					}

					// Pass the child as pointer in case it is an array
					$node = &$node[$nodes[$i]];
				}
			}

			if (!is_array($node))
				// Convert the node to array to make append possible
			{
				$node = get_object_vars($node);
			}

			$node[] = $value;
			$result = $value;
		}

		return $result;
	}

	/**
	 * Delete a registry value
	 *
	 * @param   string  $path  Registry Path
	 *
	 * @return  mixed  The value of the removed node or null if not set
	 *
	 * @since   1.6.0
	 */
	public function remove($path)
	{
		// Cheap optimisation to direct remove the node if there is no separator
		if (!strpos($path, $this->separator))
		{
			$result = (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '') ? $this->data->$path : null;

			unset($this->data->$path);

			return $result;
		}

		/*
		 * Explode the registry path into an array and remove empty
		 * nodes that occur as a result of a double separator. ex: wp..test
		 * Finally, re-key the array so they are sequential.
		 */
		$nodes = array_values(array_filter(explode($this->separator, $path), 'strlen'));

		if (!$nodes)
		{
			return null;
		}

		// Initialize the current node to be the registry root.
		$node   = $this->data;
		$parent = null;

		// Traverse the registry to find the correct node for the result.
		for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++)
		{
			if (is_object($node))
			{
				if (!isset($node->{$nodes[$i]}) && ($i !== $n))
				{
					continue;
				}

				$parent = &$node;
				$node   = $node->{$nodes[$i]};

				continue;
			}

			if (is_array($node))
			{
				if (($i !== $n) && !isset($node[$nodes[$i]]))
				{
					continue;
				}

				$parent = &$node;
				$node   = $node[$nodes[$i]];

				continue;
			}
		}

		// Get the old value if exists so we can return it
		switch (true)
		{
			case (is_object($node)):
				$result = isset($node->{$nodes[$i]}) ? $node->{$nodes[$i]} : null;
				unset($parent->{$nodes[$i]});
				break;

			case (is_array($node)):
				$result = isset($node[$nodes[$i]]) ? $node[$nodes[$i]] : null;
				unset($parent[$nodes[$i]]);
				break;

			default:
				$result = null;
				break;
		}

		return $result;
	}

	/**
	 * Transforms a namespace to an array
	 *
	 * @return  array  An associative array holding the namespace data
	 *
	 * @since   1.0
	 */
	public function toArray()
	{
		return (array) $this->asArray($this->data);
	}

	/**
	 * Transforms a namespace to an object
	 *
	 * @return  object   An an object holding the namespace data
	 *
	 * @since   1.0
	 */
	public function toObject()
	{
		return $this->data;
	}

	/**
	 * Get a namespace in a given string format
	 *
	 * @param   string  $format   Format to return the string in
	 * @param   mixed   $options  Parameters used by the formatter, see formatters for more info
	 *
	 * @return  string   Namespace in string format
	 *
	 * @since   1.0
	 */
	public function toString($format = 'JSON', $options = array())
	{
		// Return a namespace in a given format
		$handler = Vendors\AbstractRegistryFormat::getInstance($format, $options);

		return $handler->objectToString($this->data, $options);
	}

	/**
	 * Method to recursively bind data to a parent object.
	 *
	 * @param   object   $parent     The parent object on which to attach the data values.
	 * @param   mixed    $data       An array or object of data to bind to the parent object.
	 * @param   boolean  $recursive  True to support recursive bindData.
	 * @param   boolean  $allowNull  True to allow null values.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function bindData($parent, $data, $recursive = true, $allowNull = true)
	{
		// The data object is now initialized
		$this->initialized = true;

		// Ensure the input data is an array.
		$data = is_object($data) ? get_object_vars($data) : (array) $data;

		foreach ($data as $k => $v)
		{
			if (!$allowNull && !(($v !== null) && ($v !== '')))
			{
				continue;
			}

			if ($recursive && ((is_array($v) && \FPFramework\Helpers\ArrayHelper::isAssociative($v)) || is_object($v)))
			{
				if (!isset($parent->$k))
				{
					$parent->$k = new \stdClass;
				}

				$this->bindData($parent->$k, $v);

				continue;
			}

			if ($k == '' && !is_numeric($k))
			{
				continue;
			}
			
			$parent->$k = $v;
		}
	}

	/**
	 * Method to recursively convert an object of data to an array.
	 *
	 * @param   object  $data  An object of data to return as an array.
	 *
	 * @return  array  Array representation of the input object.
	 *
	 * @since   1.0
	 */
	protected function asArray($data)
	{
		$array = array();

		if (is_object($data))
		{
			$data = get_object_vars($data);
		}

		foreach ($data as $k => $v)
		{
			if (is_object($v) || is_array($v))
			{
				$array[$k] = $this->asArray($v);

				continue;
			}

			$array[$k] = $v;
		}

		return $array;
	}

	/**
	 * Dump to one dimension array.
	 *
	 * @param   string  $separator  The key separator.
	 *
	 * @return  string[]  Dumped array.
	 *
	 * @since   1.3.0
	 */
	public function flatten($separator = null)
	{
		$array = array();

		if (empty($separator))
		{
			$separator = $this->separator;
		}

		$this->toFlatten($separator, $this->data, $array);

		return $array;
	}

	/**
	 * Method to recursively convert data to one dimension array.
	 *
	 * @param   string        $separator  The key separator.
	 * @param   array|object  $data       Data source of this scope.
	 * @param   array         $array      The result array, it is passed by reference.
	 * @param   string        $prefix     Last level key prefix.
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 */
	protected function toFlatten($separator = null, $data = null, &$array = array(), $prefix = '')
	{
		$data = (array) $data;

		if (empty($separator))
		{
			$separator = $this->separator;
		}

		foreach ($data as $k => $v)
		{
			$key = $prefix ? $prefix . $separator . $k : $k;

			if (is_object($v) || is_array($v))
			{
				$this->toFlatten($separator, $v, $array, $key);

				continue;
			}

			$array[$key] = $v;
		}
	}
}