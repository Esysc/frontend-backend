// eslint.config.js
import js from '@eslint/js'
import vue from 'eslint-plugin-vue'
import tseslint from 'typescript-eslint'
import vueParser from 'vue-eslint-parser'

export default [
  { ignores: ['dist', 'node_modules'] },
  js.configs.recommended,
  ...vue.configs['flat/recommended'],
  ...tseslint.configs.recommended,
  {
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        browser: true,
        node: true,
        es2021: true,
      },
      parser: vueParser, // ✅ Vue parser FIRST
      parserOptions: {
        parser: tseslint.parser, // ✅ TypeScript parser for <script>
        ecmaFeatures: {
          jsx: true,
        },
      },
    },
  },
]
