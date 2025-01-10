<?php

namespace Flossiraptor\Imds4azure;

/**
 * Data wrapper to query Azure IMDS metadata.
 */
class Metadata implements MetadataInterface {

  /**
   * Constructor.
   *
   * @param \stdClass $metadata
   *   Metadata provided by the Azure IMDS.
   */
  public function __construct(protected \stdClass $metadata) {
  }

  /**
   * {@inheritdoc}
   */
  public function get(?string $resource = NULL) : object|array|string|null {
    if (empty($resource)) {
      return $this->metadata;
    }
    $keys = explode('.', $resource);
    return $this->nestedFetch($keys);
  }

  /**
   * {@inheritdoc}
   */
  public function has(string $resource) : bool {
    return in_array($resource, $this->keys());
  }

  /**
   * {@inheritdoc}
   */
  public function keys() : array {
    $keys = $this->nestedKeyFetch($this->metadata);
    natsort($keys);
    return $keys;
  }

  /**
   * Recursively iterate metadata to retrieve an identifier for each property.
   *
   * @param mixed $subset
   *   The subtree of metadata to process.
   * @param array $prefix
   *   Identifiers of the hierarchy for this subtree.
   *
   * @return string[]
   *   List of property identifiers for the provided subtree.
   */
  protected function nestedKeyFetch($subset, array $prefix = []) : array {
    $keys = [];
    switch (gettype($subset)) {
      case 'object':
        foreach (array_keys(get_object_vars($subset)) as $key) {
          $keys[] = $this->keyName($key, $prefix);
          $keys = array_merge(
            $keys,
            $this->nestedKeyFetch($subset->$key, array_merge($prefix, [$key]))
          );
        }
        break;

      case 'array':
        foreach (array_keys($subset) as $key) {
          $keys[] = $this->keyName($key, $prefix);
          $keys = array_merge(
            $keys,
            $this->nestedKeyFetch($subset[$key], array_merge($prefix, [$key]))
          );
        }
        break;
    }
    return $keys;
  }

  /**
   * Fetch a subset of the metadata, defined by a set of property keys.
   *
   * @param array $keys
   *   Identifier for the subset to fetch.
   *
   * @return mixed
   *   The subset of metadata requested, or NULL if the keys do not exist.
   */
  protected function nestedFetch(array $keys = []) {
    $result = $this->metadata;
    foreach ($keys as $key) {
      switch(gettype($result)) {
        case 'object':
          $result = $result->$key ?? NULL;
          break;

        case 'array':
          $result = $result[$key] ?? NULL;
          break;

        default:
          $result = NULL;
          break;
      }
    }
    return $result;
  }

  /**
   * Generate a key name for a nested property.
   *
   * @param string $key
   *   The name of the property.
   * @param string[] $prefix
   *   The parent hierarchy for the property.
   *
   * @return string
   *   The dot-separated identifier for the property.
   */
  protected function keyName(string $key, array $prefix = []) : string {
    return implode('.', array_merge($prefix, [$key]));
  }

}
