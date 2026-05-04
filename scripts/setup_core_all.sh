#!/usr/bin/env bash
set -euo pipefail

case "$OSTYPE" in
  msys*|cygwin*|win32*)
    echo "Detected Windows environment. Executing PowerShell setup..."
    powershell -ExecutionPolicy Bypass -File "./scripts/setup_core.ps1"
    ;;
  linux*|darwin*)
    echo "Detected Unix-like environment. Executing Bash setup..."
    bash "./scripts/setup_core.sh"
    ;;
  *)
    echo "Unsupported OS: $OSTYPE"; exit 1
    ;;
esac
