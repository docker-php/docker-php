# Documentation development

The Docker PHP 3.0 documentation uses Mintlify. Its source lives in `docs/` in
this repository, alongside the client code. This replaces the legacy MkDocs
configuration; earlier documentation remains available in Git history.

The site is being prepared. No hosted Mintlify URL or custom domain is configured
in the repository, and the upcoming package releases are still marked as
unpublished.

## Local preview

Use Node.js 22 or 24 LTS and PHP 8.1 or later. From the repository root:

```bash
cd docs
npm ci
npm run dev
```

Open the localhost URL printed by the CLI. `docs/package-lock.json` pins the
tooling used by the preview and CI.

## Validation

From `docs/`:

```bash
npm run validate
npm run check:links
npm run check:examples
```

The first two commands use Mintlify's configuration/MDX and internal-link
validators. The last command checks PHP snippet syntax, not runtime behavior.
Test examples which mutate Docker state against a development daemon.

Selected complex recipes also have mocked HTTP tests in
`tests/DocumentationExamplesTest.php`. With the matching API package installed,
run them from the repository root:

```bash
vendor/bin/phpunit tests/DocumentationExamplesTest.php
```

These tests execute the PHP blocks extracted from the MDX pages, replacing
client creation with a fake transport and local paths with disposable fixtures.
They check request shapes, stream consumption and error handling without a
Docker daemon or real registry credentials. They do not validate daemon-side
builds, mounts, transfers, logging drivers or transport compatibility. The
normal PHP test workflow includes them; the lighter documentation workflow
checks syntax and links only.

## Tooling updates

Mintlify is development tooling, not a Composer dependency. Check `npm audit`
when updating it and re-run the validation commands. The initial audit of
`mint` 4.2.986 on 5 October 2026 reported 21 affected dependency packages,
including 18 rated high severity. Some suggested fixes downgrade the CLI;
no forced fixes or dependency overrides have been applied. Review the upstream
fixes before updating the lock file, and use only trusted documentation and
assets with the local tooling.

## Hosted setup

Connect the Mintlify project to these settings when the site is ready to deploy:

| Setting | Value |
| --- | --- |
| GitHub repository | `docker-php/docker-php` |
| Deployment branch | `main` |
| `docs.json` in a subdirectory | Enabled |
| Documentation path | `/docs` |
| Configuration file | `docs/docs.json` |

Use [Mintlify's monorepo setup](https://www.mintlify.com/docs/deploy/monorepo),
not a new template repository. The GitHub app needs access only to this public
repository. Check its requested permissions before authorizing installation.
Do not grant access to the Cylo repositories.

The configuration uses the standard theme and built-in components. Start with
the free Starter plan; do not assume eligibility for sponsored OSS Pro access
or enable a paid trial or subscription as part of this setup.

Mintlify deploys from the connected repository. Commit and push only when you
intend those documentation changes to reach the connected site. A dashboard
connection, repository update or custom-domain change can publish the site;
review the draft and confirm the destination before doing that.

## Before replacing the old documentation link

- Review the v3.0 migration instructions and test their examples.
- Confirm the connected repository, branch and docs path.
- Check the hosted site's navigation, mobile layout and internal links.
- Choose a documentation hostname and configure its DNS separately.
- Replace the legacy README links only after the new site is reachable.
- Remove the unreleased notices when both package releases are published.
