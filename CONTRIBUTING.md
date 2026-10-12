# Contributing to WP Duplicate Content

Thanks for helping improve this WordPress plugin! Contributions that fix bugs, improve security, add tests, or clarify documentation are welcome.

## Before you start

1. Search [existing issues](https://github.com/benmustafademirci/wp-duplicate-content/issues) and pull requests to avoid duplicate work.
2. For a new feature, open an issue describing the use case and proposed behavior first.
3. For a security vulnerability, **do not publish exploit details in a public issue**. Contact the maintainer privately so the problem can be addressed responsibly.

## Local development

You need a working local WordPress instance and a PHP runtime.

1. Clone the repository.
2. Copy or symlink the plugin directory into `wp-content/plugins/wp-duplicate-content/` in your local WordPress installation.
3. Activate **WP Duplicate Content** in the WordPress admin panel.
4. Test changes on a disposable site, rather than on a production installation.

Check PHP syntax before submitting a change:

```bash
php -l wp-duplicate-content.php
```

The syntax check does **not** replace running the plugin inside WordPress.

## Manual verification

Use [TESTING.md](TESTING.md) for existing regression cases. For a changed feature, include:

- WordPress version and PHP version
- Steps to reproduce the original behavior
- Steps used to test the change
- Expected outcome and **actual observed** outcome
- Notes for any case that was **not run**

For duplication changes, test Posts, Pages, relevant custom post types, permissions, and invalid nonces when applicable.

## Pull request guidelines

1. Create a branch with a descriptive name, e.g. `fix/duplicate-post-status`.
2. Keep the change focused; unrelated changes should go into separate pull requests.
3. Follow the existing WordPress coding conventions and escape or validate any untrusted input.
4. Update documentation or test instructions when behavior changes.
5. Open a pull request explaining **what** changed, **why**, and **how it was verified**.
6. If a test was not performed, say so instead of reporting it as passing.

Use accurate commit authorship. Add a `Co-authored-by:` trailer only when the named person genuinely co-authored that commit and agrees to be credited.

## License

By contributing, you agree that your contribution will be available under the project's existing GPL-2.0 license terms.
