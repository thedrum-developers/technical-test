# Sample Development Environment

## NGINX

Place an appropriate NGINX configuration file into `nginx/sites-enabled` and it will be picked up by the Docker container.

## MySQL

The credentials for the MySQL server are `root` and `secret`.

## Xdebug

The PHP-FPM container comes with Xdebug installed, but requires additional config to have it connect back to your host machine from the container.

You will find a `.env.dist` file in this directory - if you copy it to `.env` and update the IP address / host using the directions below, Xdebug should function.

### OSX

Docker for Mac provides an easy way to access the host machine using `docker.for.mac.localhost` - setting `XDEBUG_REMOTE_HOST` to this value should allow Xdebug to connect to your host machine.

### Windows

Docker for Windows provides a similar mechanism to OSX, where `host.docker.internal` should be used as the `XDEBUG_REMOTE_HOST` value.

### Linux

Docker on Linux does not provide an easy method to access the host machine, however the following command should give you the IP address of Docker's bridge network which should allow Xdebug to connect:

```
docker network inspect --format="{{(index (index .IPAM.Config) 0).Gateway}}" bridge
```
