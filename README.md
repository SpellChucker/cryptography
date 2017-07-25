Trying out some interesting cryptography using PHP.

#### Info
This tool will attempt to decrypt a substitution cipher encrypted string
by using quadgrams to determine the fitness score of various key generations.
The key generations use the hill climbing algorithm to determine the best score.

#### How to run
Run this by running: `php decrypt.php -e <path-to-encrypted-file>`

#### Output
This will output the decrypted text into the `decrypted_text` folder.

#### Tests
There are two files in the `encrypted_files` directory to test with.
