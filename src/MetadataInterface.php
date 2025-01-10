<?php

namespace Flossiraptor\Imds4azure;

/**
 * Query and fetch properties of instance metadata.
 */
interface MetadataInterface {

  /**
   * Retrieve the instance metadata.
   *
   * @param string $resource
   *   (optional) Dot-separated property keys to retrieve a subset of data.
   *
   * @return object|array|string|null
   *   The instance metadata requested.
   *   If a resource is specified and the resource doesn't exist, NULL is
   *   returned.
   */
  public function get(?string $resource = NULL) : object|array|string|null;

  /**
   * Check whether a property exists.
   *
   * @param string $resource
   *   Dot-separated property key, such as "network.interface.1".
   *
   * @return bool
   *   TRUE if the property is present in the instance metadata.
   */
  public function has(string $resource) : bool;

  /**
   * List the metadata properties available.
   *
   * @return string[]
   *   List of metadata properties available, as dot-separated identifiers.
   */
  public function keys() : array;

}
