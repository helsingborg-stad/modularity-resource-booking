module.exports = {
    env: {
        browser: true,
    },
    globals: {
        React: 'writreadableable',
        ReactDOM: 'readable',
        modResourceBookingForm: 'readable',
        modOrderHistory: 'readable',
        modRegistrationForm: 'readable',
        modUserAccount: 'readable',
        hyperform: 'readable',
    },
    extends: ['airbnb/base', 'prettier'],
    plugins: ['prettier', 'react'],
    parser: 'babel-eslint',
    parserOptions: {
        ecmaVersion: 2018,
        sourceType: 'module',
        ecmaFeatures: {
            jsx: true,
        },
    },
    rules: {
        'prettier/prettier': ['error'],
        'arrow-spacing': 0,
        'comma-dangle': 0,
        'func-names': 0,
        'object-shorthand': 0,
        'newline-after-var': 0,
        'no-console': 'warn',
        'no-invalid-this': 0,
        'object-curly-spacing': [2, 'always'],
        'prefer-arrow-callback': 0,
        'prefer-const': 1,
        'prefer-reflect': 0,
        'space-before-function-paren': [0, { anonymous: 'always', named: 'always' }],
        'class-methods-use-this': 0,
        'react/jsx-uses-vars': [2],
        'no-shadow': 0,
        'no-plusplus': 0,
        'no-new': 0,
        'no-unused-vars': [1, { vars: 'local', args: 'none' }],
        'no-param-reassign': 0,
        radix: 0,
        'no-multi-assign': 0,
        'no-alert': 0,
    },
};
