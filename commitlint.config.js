export default {
  parserPreset: {
    parserOpts: {
      headerPattern: /^\[(\w+)\]\s(.+)$/,
      headerCorrespondence: ['type', 'subject'],
    },
  },
  rules: {
    'type-enum': [
      2,
      'always',
      [
        'feat', // New feature
        'fix', // Bug fix
        'docs', // Documentation only changes
        'style', // Changes that don't affect the meaning of the code (formatting, etc)
        'refactor', // Code change that neither fixes a bug nor adds a feature
        'perf', // Performance improvement
        'test', // Adding missing tests or correcting existing tests
        'build', // Changes that affect the build system or external dependencies
        'ci', // Changes to CI configuration files and scripts
        'chore', // Other changes that don't modify src or test files
        'revert', // Reverts a previous commit
      ],
    ],
    'subject-case': [0], // Allow any case for subject
    'subject-max-length': [2, 'always', 100],
    'body-max-line-length': [2, 'always', 100],
    'type-empty': [2, 'never'], // Type is required
    'subject-empty': [2, 'never'], // Subject is required
    'header-max-length': [2, 'always', 100], // Max header length
  },
};
