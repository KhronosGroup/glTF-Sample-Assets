#!/usr/bin/bash
#
# Auto-update and license verification for glTF-Sample-Asset repository
#
# Copyright: 2023, The Khronos Group.
# Author: Leonard Daly, Daly Realism
# 
# SPDX-FileCopyrightText: 2023, The Khronos Group
#  
# SPDX-License-Identifier: Apache-2.0
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
# 
#   http://www.apache.org/licenses/LICENSE-2.0
# 
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

# Run everything from repo 'util' directory
# Assumes PHP & reuse applications are installed and available as 'php' and 'reuse', respectively
#	Set $PATH to include the directories where those applications reside
#	Note that reuse is a python script

pushd ..
php util/model.php --no-warn --update --process-repo
reuse lint >& ./.reuse/dep5.error
reuse spdx -o ./.reuse/reuse.spdx

