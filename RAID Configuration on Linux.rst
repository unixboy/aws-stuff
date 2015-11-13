Creating a RAID Array on Linux

Use the following procedure to create the RAID array. Note that you can get directions for Windows instances from Creating a RAID Array on Windows in the Amazon EC2 User Guide for Microsoft Windows Instances.

To create a RAID array on Linux

Create the Amazon EBS volumes for your array. For more information, see Creating an Amazon EBS Volume.

Important

Create volumes with identical size and IOPS performance values for your array. Make sure you do not create an array   that exceeds the available bandwidth of your EC2 instance. For more information, see Amazon EC2 Instance Configuratin.

Attach the Amazon EBS volumes to the instance that you want to host the array. For more information, see Attaching an 

Amazon EBS Volume to an Instance.

Use the mdadm command to create a logical RAID device from the newly attached Amazon EBS volumes. Substitute the number of volumes in your array for number_of_volumes and the device names for each volume in the array (such as /dev/xvdf) for device_name. You can also substitute MY_RAID with your own unique name for the array.

Note

You can list the devices on your instance with the lsblk command to find the device names.

 (RAID 0 only) To create a RAID 0 array, execute the following command (note the --level=0 option to stripe the array):

 ::
 
  [ec2-user ~]$ sudo mdadm --create --verbose /dev/md0 --level=0 --name=MY_RAID --raid-devices=number_of_volumes device_name1 device_name2


(RAID 1 only) To create a RAID 1 array, execute the following command (note the --level=1 option to mirror the array):

::

 [ec2-user ~]$ sudo mdadm --create --verbose /dev/md0 --level=1 --name=MY_RAID --raid-devices=number_of_volumes device_name1 device_name2
    
    
    
    
Create a file system on your RAID array, and give that file system a label to use when you mount it later. For example, to create an ext4 file system with the label MY_RAID, execute the following command:

::

 [ec2-user ~]$ sudo mkfs.ext4 -L MY_RAID /dev/md0

Depending on the requirements of your application or the limitations of your operating system, you can use a different file system type, such as ext3 or XFS (consult your file system documentation for the corresponding file system creation command).

Create a mount point for your RAID array.

::
 
 [ec2-user ~]$ sudo mkdir -p /mnt/raid

Finally, mount the RAID device on the mount point that you created:

::

 [ec2-user ~]$ sudo mount LABEL=MY_RAID /mnt/raid

Your RAID device is now ready for use.

(Optional) To mount this Amazon EBS volume on every system reboot, add an entry for the device to the /etc/fstab file.

Create a backup of your /etc/fstab file that you can use if you accidentally destroy or delete this file while you are editing it.

 ::
   
  [ec2-user ~]$ sudo cp /etc/fstab /etc/fstab.orig


 Open the /etc/fstab file using your favorite text editor, such as nano or vim.

 Add a new line to the end of the file for your volume using the following format.

 device_label  mount_point  file_system_type  fs_mntops  fs_freq  fs_passno  

The last three fields on this line are the file system mount options, the dump frequency of the file system, and the order of file system checks done at boot time. If you don't know what these values should be, then use the values in the example below for them (defaults,nofail 0 2). For more information on /etc/fstab entries, see the fstab manual page (by entering man fstab on the command line). For example, to mount the ext4 file system on the device with the label MY_RAID at the mount point /mnt/raid, add the following entry to /etc/fstab.

Note

If you ever intend to boot your instance without this volume attached (for example, so this volume could move back and forth between different instances), you should add the nofail mount option that allows the instance to boot even if there are errors in mounting the volume. Debian derivatives, such as Ubuntu, must also add the nobootwait mount option.
    
::

  LABEL=MY_RAID       /mnt/raid   ext4    defaults,nofail        0       2

After you've added the new entry to /etc/fstab, you need to check that your entry works. Run the sudo mount -a command to mount all file systems in /etc/fstab.

::

 [ec2-user ~]$ sudo mount -a

If the previous command does not produce an error, then your /etc/fstab file is OK and your file system will mount automatically at the next boot. If the command does produce any errors, examine the errors and try to correct your /etc/fstab.

Warning

Errors in the /etc/fstab file can render a system unbootable. Do not shut down a system that has errors in the /etc/fstab file.

(Optional) If you are unsure how to correct /etc/fstab errors, you can always restore your backup /etc/fstab file with the following command.

::

 [ec2-user ~]$ sudo mv /etc/fstab.orig /etc/fstab



    

::


 https://raid.wiki.kernel.org/index.php/Linux_Raid
