#!/usr/bin/env node

import { readFileSync } from 'fs';

// Allowed commit types with descriptions
const COMMIT_TYPES = {
  feat: 'New feature',
  fix: 'Bug fix',
  docs: 'Documentation changes',
  style: 'Code formatting (no functionality changes)',
  refactor: 'Code refactoring',
  perf: 'Performance improvements',
  test: 'Adding or modifying tests',
  build: 'Build system changes',
  ci: 'CI/CD changes',
  chore: 'General maintenance',
  revert: 'Revert previous commits',
};

// Pattern for commit message: [type] Description
const COMMIT_PATTERN =
  /^\[(feat|fix|docs|style|refactor|perf|test|build|ci|chore|revert)\]\s+.+$/;

function validateCommitMessage() {
  try {
    // Read commit message from temporary file
    const commitMsgFile = process.argv[2];
    if (!commitMsgFile) {
      console.error('❌ Error: Commit message file not provided');
      process.exit(1);
    }

    const commitMsg = readFileSync(commitMsgFile, 'utf8').trim();

    // Skip validation for merge commits
    if (commitMsg.startsWith('Merge') || commitMsg.startsWith('merge')) {
      console.log('✅ Merge commit detected, validation skipped');
      process.exit(0);
    }

    // Validate pattern
    if (!COMMIT_PATTERN.test(commitMsg)) {
      console.error('❌ Invalid commit message format!');
      console.error('');
      console.error('Message must follow the pattern: [type] Description');
      console.error('');
      console.error('Valid types:');
      Object.entries(COMMIT_TYPES).forEach(([type, description]) => {
        console.error(`  • ${type.padEnd(10)} - ${description}`);
      });
      console.error('');
      console.error('Valid examples:');
      console.error('  • [feat] Add user authentication system');
      console.error('  • [fix] Correct bug in login form');
      console.error('  • [docs] Update API documentation');
      console.error('');
      console.error(`Your message: "${commitMsg}"`);
      process.exit(1);
    }

    console.log('✅ Valid commit message format!');
    process.exit(0);
  } catch (error) {
    console.error('❌ Error validating commit:', error.message);
    process.exit(1);
  }
}

validateCommitMessage();
