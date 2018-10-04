#!/usr/bin/env sh

# This entrypoint acts as a wrapper to allow SIGINTs when using `docker run`

# The following trap isn't strictly necessary, but it does move the prompt
# to a newline on termination.
trap 'echo && exit 0' INT

"$@"
