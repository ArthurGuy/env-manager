# .env Manager

This is a simple app for securely managing .env files.
Files can be created and edited using the application while the details of who last viewed the file or made changes are maintained.
The file content is encrypted using Amazons KMS service and stored in a database, this means only those people with access
 to the KMS key can decrypt the file contents.

During the provisioning of servers the relevant .env file can be fetched from this service, the file is returned in encrypted form
where it can be easily decoded on the target server providing it has been granted permission.

This means that to access the file data someone needs to have a valid login for this service or access the the KMS key.