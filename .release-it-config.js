export default {
  git: {
    commitMessage: 'chore: release v${version}',
    tagName: 'v${version}',
    requireCleanWorkingDir: false,
    requireBranch: false,
  },
  github: {
    release: false,
  },
  npm: {
    publish: false,
  },
  plugins: {
    '@release-it/conventional-changelog': {
      preset: {
        name: 'conventionalcommits',
        types: [
          { type: 'feat', section: '✨ Features' },
          { type: 'fix', section: '🐛 Bug Fixes' },
          { type: 'perf', section: '⚡ Performance Improvements' },
          { type: 'revert', section: '⏪ Reverts' },
          { type: 'docs', section: '📚 Documentation' },
          { type: 'style', section: '💎 Styles' },
          { type: 'refactor', section: '♻️ Code Refactoring' },
          { type: 'test', section: '✅ Tests' },
          { type: 'build', section: '🔨 Build System' },
          { type: 'ci', section: '👷 CI/CD' },
          { type: 'chore', hidden: true },
        ],
      },
      infile: 'CHANGELOG.md',
      header:
        '# Changelog\n\nAll notable changes to this project will be documented in this file.\n\nThe format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),\nand this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).\n',
      parserOpts: {
        headerPattern: /^\[(\w+)\]\s(.+)$/,
        headerCorrespondence: ['type', 'subject'],
      },
      writerOpts: {
        transform: (commit) => {
          // Parse [type] format
          const match = commit.header?.match(/^\[(\w+)\]\s(.+)$/);
          if (match) {
            return {
              ...commit,
              type: match[1],
              subject: match[2],
            };
          }
          return commit;
        },
      },
      whatBump: (commits) => {
        let level = 2; // patch
        let breakings = 0;
        let features = 0;

        commits.forEach((commit) => {
          // Parse commit message with [type] format
          const match = commit.header?.match(/^\[(\w+)\]\s(.+)$/);
          const type = match ? match[1] : commit.type;

          if (commit.notes.length > 0) {
            breakings += commit.notes.length;
            level = 0; // major
          } else if (type === 'feat') {
            features += 1;
            if (level === 2) {
              level = 1; // minor
            }
          }
        });

        return {
          level,
          reason:
            breakings > 0
              ? `There are ${breakings} BREAKING CHANGES`
              : features > 0
                ? `There are ${features} new features`
                : 'Patch release',
        };
      },
    },
  },
  hooks: {
    'before:init': ['npm run lint', 'vendor/bin/pint'],
  },
};
