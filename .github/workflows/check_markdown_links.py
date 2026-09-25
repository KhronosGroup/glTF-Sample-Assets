#!/usr/bin/env python
# -*- coding: utf-8 -*-

# SPDX-FileCopyrightText: The Khronos Group Inc.

"""
Markdown link checker.
Validates that all internal links in markdown files point to existing files and anchors.
SPDX-License-Identifier: CC0-1.0
"""

import re
import sys
from pathlib import Path
from typing import Dict, List, Set, Tuple
from urllib.parse import unquote


# Find all markdown files in the given directory.
def find_markdown_files(root_path: Path) -> List[Path]:
    md_files = []
    for path in root_path.rglob("*.md"):
        md_files.append(path)
    return md_files


# Extract markdown links from content.
# Returns list of (link_target, line_number, full_match) tuples.
def extract_links(content: str, file_path: Path) -> List[Tuple[str, int, str]]:
    links = []
    # Match markdown links: [text](url) or [text](url#anchor)
    # Also match reference-style links: [text][ref] and [ref]: url
    link_pattern = r"\[([^\]]+)\]\(([^)]+)\)"
    for line_num, line in enumerate(content.split("\n"), 1):
        for match in re.finditer(link_pattern, line):
            link_target = match.group(2)
            # Skip external links (http, https, mailto, etc.)
            if re.match(r"^[a-zA-Z][a-zA-Z0-9+.-]*:", link_target):
                continue
            links.append((link_target, line_num, match.group(0)))
    return links


# Extract all valid anchor targets from markdown content.
# Anchors can come from headers or explicit anchor tags.
def extract_anchors(content: str) -> Set[str]:
    anchors = set()
    # Extract headers (# Header -> #header)
    for line in content.split("\n"):
        # Match markdown headers.
        header_match = re.match(r"^(#{1,6})\s+(.+)$", line.strip())
        if header_match:
            header_text = header_match.group(2)
            # Convert to anchor format: lowercase, replace spaces with hyphens, remove special chars.
            anchor = header_text.lower()
            anchor = re.sub(r"[^a-z0-9\s-]", "", anchor)
            anchor = re.sub(r"\s+", "-", anchor.strip())
            anchors.add(anchor)
    # Also match explicit HTML anchors: <a name="anchor"></a> or <a id="anchor"></a>
    anchor_pattern = r'<a\s+(?:name|id)=["\']([^"\']+)["\']'
    for match in re.finditer(anchor_pattern, content):
        anchors.add(match.group(1))
    return anchors


# Check if a link is valid.
# Returns (is_valid, error_message) tuple.
def check_link(link: str, source_file: Path, root_path: Path, anchor_cache: Dict[Path, Set[str]]) -> Tuple[bool, str]:
    # Split link into path and anchor.
    if "#" in link:
        path_part, anchor = link.split("#", 1)
    else:
        path_part, anchor = link, None
    path_part = unquote(path_part)
    # Handle empty path (link to anchor in same file).
    if not path_part:
        target_file = source_file
    elif path_part.startswith("/"):
        # Root-relative link: resolve from the repository root.
        target_file = (root_path / path_part.lstrip("/")).resolve()
    else:
        # Resolve relative path from source file's directory.
        source_dir = source_file.parent
        target_file = (source_dir / path_part).resolve()
    # Check if target file exists
    if not target_file.exists():
        return False, f"Target file does not exist: {target_file.relative_to(root_path)}"
    # If there's an anchor, check if it exists in the target file.
    if anchor:
        # Load anchors from cache or extract them.
        if target_file not in anchor_cache:
            try:
                with open(target_file, "r", encoding="utf-8") as f:
                    content = f.read()
                    anchor_cache[target_file] = extract_anchors(content)
            except Exception as e:
                return False, f"Could not read target file for anchor check: {e}"
        if anchor not in anchor_cache[target_file]:
            return False, f"Anchor '#{anchor}' not found in {target_file.relative_to(root_path)}"
    return True, ""


def main():
    if len(sys.argv) < 2:
        print("Usage: check_markdown_links.py <file1.md> [file2.md ...]")
        print("   or: check_markdown_links.py --all")
        sys.exit(1)
    # Determine root path (repository root).
    script_path = Path(__file__).resolve()
    root_path = script_path.parent.parent.parent
    # Get list of files to check.
    if sys.argv[1] == "--all":
        files_to_check = find_markdown_files(root_path / "specification")
    else:
        files_to_check = [Path(f).resolve() for f in sys.argv[1:]]
    # Cache for anchors in files.
    anchor_cache: Dict[Path, Set[str]] = {}
    # Track errors.
    errors = []
    files_checked = 0
    links_checked = 0
    for md_file in files_to_check:
        if not md_file.exists():
            errors.append(f"File does not exist: {md_file}")
            continue
        files_checked += 1
        try:
            with open(md_file, "r", encoding="utf-8") as f:
                content = f.read()
        except Exception as e:
            errors.append(f"Could not read {md_file}: {e}")
            continue
        # Extract and check all links.
        links = extract_links(content, md_file)
        for link_target, line_num, full_match in links:
            links_checked += 1
            is_valid, error_msg = check_link(link_target, md_file, root_path, anchor_cache)
            if not is_valid:
                rel_path = md_file.relative_to(root_path)
                errors.append(f"{rel_path}:{line_num}: {error_msg}")
                errors.append(f"  Link: {full_match}")
    # Print results.
    if errors:
        print(f"❌ Found {len(errors)//2} broken link(s) in {files_checked} file(s):")
        print()
        for error in errors:
            print(error)
        sys.exit(1)
    else:
        print(f"✅ All {links_checked} link(s) in {files_checked} markdown file(s) are valid!")
        sys.exit(0)


if __name__ == "__main__":
    main()
