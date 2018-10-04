#!/usr/bin/env sh

env_file=".env.dist"

die() {
    echo "Error: $1" 1>&2 && exit 1
}

check_for_env_file() {
    if [ -f ".env" ]; then
        env_file=".env"
    fi
}

# Initialise common variables.
init_vars() {
    user=$(id -u)
    build_dir=
    image=
    check_for_env_file
    # Disable ShellCheck warnings for unused variables in `.env` file.
    # shellcheck disable=SC2034 source=utils/common/.env
    . "$script_dir/$common_dir/$env_file"
}

# Fallback to using Docker's `bridge` network if the user defined network
# doesn't exist yet.
network_fallback() {
    if ! docker network ls | grep -q "$network"; then
        echo "Docker network \`$network\` does not exist; using default bridge instead."
        export network=bridge
    fi
}

# Handle the options passed by the `init` function in the parent script.
# Loop will break when an unexpected argument is supplied.
parse_options() {
    while :; do
        case "$1" in
            network)
                network_fallback
                ;;
            php)
                build_dir="$php_build_dir"
                export image="$php_image"
                ;;
            composer)
                build_dir="$composer_build_dir"
                export image="$composer_image"
                ;;
            *)
                break
        esac
        shift
    done
}

# Simple argument parser to ensure the scripts also work in non-interactive mode,
# eg. updating Composer via PhpStorm
# Loop will continue until all arguments have been checked.
parse_args() {
    while :; do
        case "$1" in
            --no-ansi)
                export ansi=
                ;;
            --no-interaction)
                export interactive=
                ;;
            *)
                if [ ! "$1" ]; then
                    break
                fi
        esac
        shift
    done
}

# Parse and process all the arguments passed to this script.
parse() {
    # Disable ShellCheck warning as word splitting in `parse_options` is
    # desired behaviour.
    # shellcheck disable=SC2086
    parse_options $1
    shift
    if [ "$1" ]; then
        parse_args "$@"
    fi
}

# Ensure a build directory and image name have been provided.
check_image_details() {
    if [ ! "$build_dir" ]; then
        die "No value given for \`build_dir\`."
    elif [ ! "$image" ]; then
        die "No value given for \`image\`."
    fi
}

# Build the Docker image if it doesn't exist.
build_image() {
    if ! docker images | grep -q "$image"; then
        echo "Image \`$image\` does not exist. Building in progress..."
        docker build -t "$image" "$script_dir/$build_dir" 1> /dev/null \
        || die "Failed to build image \`$image\`."
        echo "Image \`$image\` built."
    fi
}

# Assign common `docker` options to a variable for use in the parent script.
common_docker_options() {
    common_docker_opts="
        --rm
        --tty
        --volume /etc/group:/etc/group:ro
        --volume /etc/passwd:/etc/passwd:ro
        --volume /home/$user/.ssh:/home/$user/.ssh:ro
        --volume chrbrdtechtest_composer_cache:/composer/cache
        --volume $script_dir/$app_dir:$remote_dir/$app_dir
        --workdir $remote_dir/$app_dir"
}

# Shared initialisation between all utility scripts.
common_init() {
    init_vars
    parse "$@"
    check_image_details
    build_image
    common_docker_options
}
