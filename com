#!/usr/bin/env sh

script_dir=$(readlink -e "$(dirname "$0")")
# shellcheck source=utils/dcomposer
. "$script_dir/utils/dcomposer"