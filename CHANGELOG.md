# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased] 

### Changed
- Use php 8.0 as base image
- Use firebase/jwt:6, refactor related adapter package
- JWT_ALGOS (comma separated list) replaces with JWT_ALGO (single string value)
- Update composer dependencies

## [0.6.0] 2025-02-10

### Changed
- Project structure and development env, update some tools
- TimersList use empty list as default argument on construction
- Replace travis with github actions

## [0.5.0] 2020-05-05
### Changed
- Build image from php:7.4-cli

### Security
- Bump symfony/http-foundation

## [0.4.0] 2019-10-27
### Added
- Full jwt content returned. 

## [0.3.0] - 2019-02-15
### Changed
- Update Thruway to actual version

## [0.2.0] - 2017-08-25
### Added
- AbortSessionsOutOffWhitelistTimer

## [0.1.0] - 2017-08-16
### Added
- Dockerfile 
- JwtAuthentificationProvider
- FirebaseJwtDecoderAdapter
- Validators and Redis stuff. 
