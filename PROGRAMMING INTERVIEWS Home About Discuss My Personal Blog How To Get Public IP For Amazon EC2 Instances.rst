If you run "ifconfig –a" on an amazon ec2 instance (linux), it will give you the internal IP address, which is not accessible from outside world. Here is a way to get the public and private IP address for an amazon EC2 instance.

To obtain the IP addresses, issue the following HTTP queries from within the instance:

To obtain the internal IP address:

::

 curl http://169.254.169.254/latest/meta-data/local-ipv4

To obtain the public IP address:

::

 curl http://169.254.169.254/latest/meta-data/public-ipv4
