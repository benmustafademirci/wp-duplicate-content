# WP Duplicate Content — Testing Guide

## Overview

This document describes the manual testing process for the WP Duplicate Content plugin.

Feature: Preserve categories, tags, and custom taxonomy terms when duplicating WordPress posts.

Contributors:
•⁠  ⁠Mustafa Demirci (@benmustafademirci)
•⁠  ⁠Yusuf Gülen (@ygulen651)

## Test Environment

•⁠  ⁠WordPress Version: To be recorded
•⁠  ⁠PHP Version: To be recorded
•⁠  ⁠Plugin Version: 1.0.1
•⁠  ⁠Environment: Local WordPress installation
•⁠  ⁠Test Status: Not yet executed

## Test 1: Duplicate Post with Categories

Steps:
1.⁠ ⁠Create a WordPress post.
2.⁠ ⁠Assign two categories.
3.⁠ ⁠Save the post.
4.⁠ ⁠Click the Duplicate action.
5.⁠ ⁠Open the duplicated post.

Expected Result:
•⁠  ⁠A new post is created.
•⁠  ⁠The original post remains unchanged.
•⁠  ⁠Both categories are preserved.

Actual Result: Not run

## Test 2: Duplicate Post with Tags

Steps:
1.⁠ ⁠Create a post.
2.⁠ ⁠Assign three different tags.
3.⁠ ⁠Save the post.
4.⁠ ⁠Duplicate the post.
5.⁠ ⁠Inspect the duplicated post.

Expected Result:
•⁠  ⁠All three tags are copied.
•⁠  ⁠No additional tags are created.
•⁠  ⁠Original post remains unchanged.

Actual Result: Not run

## Test 3: Post Without Taxonomy Terms

Steps:
1.⁠ ⁠Create a post without tags.
2.⁠ ⁠Duplicate the post.
3.⁠ ⁠Inspect the new post.

Expected Result:
•⁠  ⁠Duplication completes successfully.
•⁠  ⁠No unexpected taxonomy terms are added.

Actual Result: Not run

## Test 4: Custom Post Types

Steps:
1.⁠ ⁠Register a custom post type with a taxonomy.
2.⁠ ⁠Create an item with assigned taxonomy terms.
3.⁠ ⁠Duplicate the item.
4.⁠ ⁠Compare the original and duplicate.

Expected Result:
•⁠  ⁠Registered taxonomy terms are preserved.
•⁠  ⁠The original item remains unchanged.

Actual Result: Not run

## Test 5: Security and Permissions

Steps:
1.⁠ ⁠Attempt duplication as a user without edit permission.
2.⁠ ⁠Attempt duplication with an invalid nonce.
3.⁠ ⁠Attempt duplication with a valid authorized request.

Expected Result:
•⁠  ⁠Unauthorized requests are rejected.
•⁠  ⁠Invalid nonces are rejected.
•⁠  ⁠Authorized requests succeed.

Actual Result: Not run

## Test Summary

| Test | Status |
|------|--------|
| Category preservation | Not run |
| Tag preservation | Not run |
| Empty taxonomy | Not run |
| Custom taxonomy | Not run |
| Permission verification | Not run |

## Notes

This document defines the test cases. Results must be updated after actual execution in a WordPress test environment.

Any discovered bugs should be documented and addressed before merging the pull request.
