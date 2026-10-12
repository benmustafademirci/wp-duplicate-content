# WP Duplicate Content — Manual Testing Guide

This guide describes how to verify the post-duplication changes introduced in #4. It is a test plan, **not a claim that tests have passed**.

## Environment

- WordPress version: _Record when tested_
- PHP version: _Record when tested_
- Plugin version: 1.0.1
- Test site / setup: _Record when tested_
- Tested by: _Record when tested_
- Test date: _Record when tested_

## Prerequisites

1. Set up a local or staging WordPress site and back it up.
2. Install and activate WP Duplicate Content.
3. Sign in as an administrator or an account with editing permission.
4. Open **Settings → Duplicate Content** and choose a default duplicated-post status.
5. Verify that the **Duplicate** action is visible in the Posts list.

## Test cases

### T1 — Categories and tags

1. Create a post with at least two categories and three tags.
2. Save it, then select **Duplicate** from the Posts list.
3. Open the newly created copy and compare its terms to the original.

**Expected:** The copy retains all selected category and tag term IDs; the source post is unchanged.

**Observed:** Not run

### T2 — No tags assigned

1. Create a post without tags (WordPress may assign a default category).
2. Duplicate it and inspect the copied post.

**Expected:** Duplication succeeds; no unrelated tags are added.

**Observed:** Not run

### T3 — Custom taxonomy

1. On a test site, register a custom taxonomy for a post type.
2. Create an item with at least two existing terms from that taxonomy.
3. Duplicate the item and compare taxonomy term assignments.

**Expected:** The duplicate retains its original registered taxonomy terms.

**Observed:** Not run

### T4 — Invalid nonce / unauthorized user

1. As a user lacking permission to edit the source post, attempt duplication.
2. As an authorized user, attempt the action with a missing or invalid nonce.
3. Repeat with a valid nonce-protected link.

**Expected:** Unauthorized/invalid requests are rejected; an authorized valid request succeeds.

**Observed:** Not run

### T5 — Original record stays unchanged

1. Record the source post ID, text and taxonomy terms.
2. Duplicate the post.
3. Reopen the original.

**Expected:** The original ID, content and taxonomy assignments are unchanged.

**Observed:** Not run

### T6 — Featured image preserved

1. Set a featured image on a post using a valid attachment in the Media Library.
2. Save the post, then duplicate it.
3. Open the duplicate and compare its featured image to the source.
4. Remove the featured image from a separate source post and duplicate that post too.

**Expected:** The duplicate with a featured image references the same existing Media Library attachment; no new attachment is created. A post without a featured image remains without one.

**Observed:** Not run

## Results summary

| Test | Result | Notes |
| --- | --- | --- |
| T1 Categories and tags | Not run | |
| T2 No tags | Not run | |
| T3 Custom taxonomy | Not run | |
| T4 Permissions and nonce | Not run | |
| T5 Original unchanged | Not run | |
| T6 Featured image | Not run | |

## Contributor checklist

- [ ] Run the applicable tests on WordPress.
- [ ] Replace `Not run` with `Pass`, `Fail` or `Blocked`, based only on actual observations.
- [ ] Record WordPress / PHP versions and who performed testing.
- [ ] Link any bugs to issues or fix commits.
- [ ] Review the changes together before merging.

**Collaboration:** @benmustafademirci prepared this checklist. @ygulen651 is invited to run tests, record observations, review the results, and propose corrections. Commit attribution should reflect actual work. A `Co-authored-by` footer should only be added to a change both contributors genuinely co-authored and have agreed to credit.
