#!/bin/bash

# Exit on first error
set -e

save_and_shutdown() {
  # save built for host result
  echo $? > /tmp/build.status
  # force clean shutdown
  halt -f
}

# make sure we shut down cleanly
trap save_and_shutdown EXIT SIGINT SIGTERM

# go back to where we were invoked
cd $WORKDIR

# can't do much without proc!
mount -t proc none /proc

# pseudo-terminal devices
mkdir -p /dev/pts
mount -t devpts none /dev/pts

# shared memory a good idea
mkdir -p /dev/shm
mount -t tmpfs none /dev/shm

# sysfs a good idea
mount -t sysfs none /sys

# pidfiles and such like
mkdir -p /var/run
mount -t tmpfs none /var/run

# takes the pain out of cgroups
cgroups-mount

# mount /var/lib/docker with a tmpfs
mount -t tmpfs none /var/lib/docker

# enable ipv4 forwarding for docker
echo 1 > /proc/sys/net/ipv4/ip_forward

# configure networking
ip addr add 127.0.0.1 dev lo
ip link set lo up
ip addr add 10.1.1.1/24 dev eth0
ip link set eth0 up
ip route add default via 10.1.1.254

# configure dns (google public)
mkdir -p /run/resolvconf
echo 'nameserver 8.8.8.8' > /run/resolvconf/resolv.conf
mount --bind /run/resolvconf/resolv.conf /etc/resolv.conf

# Start docker daemon
docker -d -H 0.0.0.0:4243 -H unix:///var/run/docker.sock 2>> /dev/null >> /dev/null &
sleep 5

DOCKER_HOST=tcp://127.0.0.1:4243 php -d default_socket_timeout=5 bin/phpunit -c phpunit.xml.dist
