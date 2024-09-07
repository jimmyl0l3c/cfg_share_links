import argparse
import os
import re
from datetime import datetime
from pathlib import Path

import requests

REPO_ROOT = Path(__file__).parent.resolve().parent.parent

GITHUB_API_VERSION = "2022-11-28"

GH_REPO_OWNER = "jimmyl0l3c"
GH_REPO_NAME = "cfg_share_links"

GH_REPO_MAIN_BRANCH = "master"
CHANGELOG_CONFIG = ".github/release.yml"
# CHANGELOG_CONFIG = ".github/release/keep-a-changelog.yml"


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        prog='PrepareRelease', description='Updates a changelog and bumps the app version'
    )
    parser.add_argument('tag')
    return parser.parse_args()


def finalize_changelog_entry(changelog_body: str) -> tuple[str, str]:
    comment_regex = re.compile(r"<!--.*?-->")
    bullet_regex = re.compile(r"\* (.+) by @\S+ in https://github.com/\S+")
    compare_link_regex = re.compile(r"\*\*Full Changelog\*\*: (https://github.com/\S+)")
    title_regex = re.compile(r"## What's Changed")
    lines = []
    compare_link = None
    for line in changelog_body.split('\n'):
        if comment_regex.match(line) or title_regex.match(line):
            continue
        if line.startswith("#"):
            if lines and lines[-1].startswith("-"):
                lines.append("")
            lines.append(line)
            lines.append("")
            if "Changed" in line:
                lines.append("- Updated translations from transifex")
        if bullet := bullet_regex.match(line):
            lines.append(f"- {bullet.group(1)}")
        if match := compare_link_regex.match(line):
            compare_link = match.group(1)
    return "\n".join(lines), compare_link


def generate_changelog_entry(tag: str) -> str:
    changelog_request = requests.post(
        headers={
            "Accept": "application/vnd.github+json",
            "X-GitHub-Api-Version": GITHUB_API_VERSION,
            "Authorization": f"Bearer {os.environ.get('GITHUB_TOKEN')}",
        },
        url=f"https://api.github.com/repos/{GH_REPO_OWNER}/{GH_REPO_NAME}/releases/generate-notes",
        json={
            "tag_name": tag,
            "target_commitish": GH_REPO_MAIN_BRANCH,
            "configuration_file_path": CHANGELOG_CONFIG,
        }
    )
    if changelog_request.status_code != 200:
        raise Exception(changelog_request.text)
    return changelog_request.json()["body"]


def update_changelog(tag: str) -> None:
    changelog_entry, compare_link = finalize_changelog_entry(generate_changelog_entry(tag))
    print(f"New changelog entry:\n\n{changelog_entry}\n")
    new_version = tag[1:]
    with (changelog_path := REPO_ROOT / "CHANGELOG.md").open("r") as changelog:
        current_changelog = changelog.read()
        updated_changelog = re.sub(
            r"## \[Unreleased]\n",
            f"## [Unreleased]\n\n## [{new_version}] - {datetime.now().strftime('%Y-%m-%d')}\n\n{changelog_entry}\n",
            current_changelog,
        )
    unreleased_regex = re.search(
        rf"\[Unreleased]: (https://github.com/{GH_REPO_OWNER}/{GH_REPO_NAME}/compare/)(v\d+.\d+.\d+)\.\.\.HEAD",
        updated_changelog)
    updated_changelog = re.sub(
        r"\[Unreleased]: .*?\n",
        f"[Unreleased]: {unreleased_regex.group(1)}{tag}...HEAD\n[{new_version}]: {compare_link}\n",
        updated_changelog
    )
    with changelog_path.open("w") as changelog:
        changelog.write(updated_changelog)


def bump_version(tag: str) -> None:
    with (app_info_path := REPO_ROOT / "appinfo" / "info.xml").open("r") as app_info:
        updated_app_info = re.sub(r"<version>\d+\.\d+\.\d+</version>", f"<version>{tag[1:]}</version>", app_info.read())
    print(f"Updated info.xml:\n\n{updated_app_info}\n")
    with app_info_path.open("w") as app_info:
        app_info.write(updated_app_info)


def main() -> None:
    if not re.match(r"v\d+\.\d+\.\d+", new_tag := parse_args().tag):
        raise Exception("Invalid tag passed")
    print("Updating changelog ...")
    update_changelog(new_tag)
    print("Bumping version ...")
    bump_version(new_tag)


if __name__ == '__main__':
    main()
