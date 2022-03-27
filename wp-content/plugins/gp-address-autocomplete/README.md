# GP Address Autocomplete

## Dependencies

[Homebrew](https://brew.sh/) is the recommended package manager for installing the following
tools on macOS. It's similar to Apt on Debian-based Linux distros but
easier to use. [Install it](https://docs.brew.sh/Installation) if you don't have it already installed.

### [Node.js and NPM](https://nodejs.org/en/)

_Node.js is a JavaScript runtime for executing JavaScript outside of the
browser environment._

We utilize Node.js and the [npm ecosystem](https://www.npmjs.com/) for transpiling [TypeScript](https://www.typescriptlang.org/) to JavaScript, packaging, and more.

Recommended Installation (macOS):

1. `brew install nvm`
 (Follow any instructions regarding adding to your shell profile)
2. `nvm install 12`
3. `nvm alias default 12`

### [Yarn](https://classic.yarnpkg.com/en/docs/install/#mac-stable)

_[Yarn](https://classic.yarnpkg.com/en/docs/install/#mac-stable) is an alternative to `npm`. It mostly does the same thing but is
generally faster and introduces new functionality sooner than `npm`._

Recommended Installation (macOS):

```
brew install yarn
```

### [direnv](https://direnv.net/)

_direnv is a handy utility for automatically setting environment variables
in your shell._

We use direnv for setting variables for acceptance tests.

Recommended Installation (macOS):

```
brew install direnv
```

## Installing Project Dependencies

After installing all of the dependencies above, you will need to install
the JavaScript by using Yarn.

This can be done by running `yarn`.

It's generally common practice to run `yarn` after
pulling from GitHub.

## Setting Project Variables

1. Copy `.envrc.sample` as `.envrc`
2. Update `GF_KEY` with your Gravity Forms key
3. Update `CYPRESS_BASE_URL` with the URL to the local site that you will run acceptance tests against.

**After the variables have been update, run the following command:**

```
direnv allow
```

## Building

Phew, now that all of the dependencies are installed, it's time to start
building!

Since we utilize TypeScript and optionally Vue in this Perk Scaffold, you
will need to transpile the TypeScript to JavaScript and package it up
in a browser-friendly format. This is done using [webpack](https://webpack.js.org/), a dependency installed
by Yarn in this scaffold.

Fortunately, this is made easy. Simply run the following:

 ```
 yarn build
 ```

 Optionally, you can use the following command to automatically build
 any time a change is detected in the files in `js/src`.

 ```
 yarn dev
 ```

## Running Acceptance Tests

Acceptance tests are our way of automating a fictitious user's browser
and going through common use-cases or edge cases that are prone to
regressions.

Our preferred framework for acceptance/E2E tests is Cypress.

To open up the Cypress GUI and run test suites individually, you can run the following:

```shell
yarn cypress open
```

If you wish to run all of the tests locally in a headless mode, run the following:

```shell
yarn cypress run
```

