# PIN Generator Package for Laravel

A Laravel Package to generate cryptographically-secure pseudorandom PINs (personal identification numbers).

Target Features:
- Each PIN comprises four numeric digits (e.g. "2845")
- "Obvious" numbers should not be allowed (e.g. "1111", "1234")
- PINs should be generated in apparently random order
- A PIN should not be repeated until all preceding valid PINs have been emitted - even if the program is restarted between PINs.

Achieved Features:
- Each PIN comprises four numeric digits (e.g. "2845")
- "Obvious" numbers should not be allowed (e.g. "1111", "1234")


## References
- [Cryptographically-secure pseudorandom number generator - Wikipedia](https://en.wikipedia.org/wiki/Cryptographically-secure_pseudorandom_number_generator)
