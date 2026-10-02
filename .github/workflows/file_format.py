#!/usr/bin/env python
# -*- coding: utf-8 -*-

# SPDX-FileCopyrightText: The Khronos Group Inc.

"""
Basic file formatting checker.
Validates that all files use UTF-8 encoding, have no BOM, and have LF line endings.
SPDX-License-Identifier: CC0-1.0
"""

import sys

if len(sys.argv) < 2:
    print("Invalid usage of file_format.py, it should be called with a path to one or multiple files.")
    sys.exit(1)

BOM = b"\xef\xbb\xbf"

changed = []
invalid = []

for file in sys.argv[1:]:
    try:
        with open(file, "rt", encoding="utf-8") as f:
            original = f.read()
    except UnicodeDecodeError:
        invalid.append(file)
        continue

    if original == "":
        continue

    EOL = "\n"

    revamp = EOL.join([line.rstrip("\n\r\t ") for line in original.splitlines(True)]).rstrip(EOL) + EOL

    new_raw = revamp.encode(encoding="utf-8")
    if new_raw.startswith(BOM):
        new_raw = new_raw[len(BOM) :]

    with open(file, "rb") as f:
        old_raw = f.read()

    if old_raw != new_raw:
        changed.append(file)
        with open(file, "wb") as f:
            f.write(new_raw)

if changed:
    for file in changed:
        print(f"FIXED: {file}")

if invalid:
    for file in invalid:
        print(f"REQUIRES MANUAL CHANGES: {file}")
    sys.exit(1)
