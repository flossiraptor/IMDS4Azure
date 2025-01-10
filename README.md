# Instance Meta-Data Service client for Azure

The Microsoft Azure IMDS (Instance Meta-Data Service) can be queried to fetch
metadata information about the running environment and Azure configuration.

## Installation

We recommend using [composer](https://getcomposer.org/) to install the IMDS for
Azure library.

```shell
  composer require flossiraptor/imds4azure
```

## Quickstart

```php
  use Flossiraptor\Imds4Azure\IMDS;
  $imds = new IMDS();

  // Get all the instance metadata.
  $instanceMetadata = $imds->instance()->get();

  // Get the public IP address of the VM.
  $ipAddress = $imds->instance()->get('network.interface.0.ipv4.ipAddress.0.publicIpAddress');

  // Get the public IP address of the load-balancer.
  $lbAddress = $imds->loadbalancer()->get('loadbalancer.publicIpAddresses.0.frontendIpAddress');
```

## Categories

Detailed descriptions and links can be found at
<https://learn.microsoft.com/en-us/azure/virtual-machines/instance-metadata-service>

### Supported

- `/metadata/identity`
- `/metadata/instance`
- `/metadata/loadbalancer`

### Not supported

- `/metadata/attested`
- `/metadata/scheduledevents`
- `/metadata/versions`

## Legal

Microsoft, Azure are trademarks of the Microsoft group of companies.
