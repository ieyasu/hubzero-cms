<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$base = 'index.php?option='.$this->option.'&controller=offering&active=progress&gid='.$this->course->get('alias');
$base .= '&offering='.$this->course->offering()->get('alias');
$base .= ($this->course->offering()->section()->get('alias') != '__default') ? ':'.$this->course->offering()->section()->get('alias') : '';
?>

<script id="progress-template-main" type="text/x-handlebars-template">
	<div class="grade-policy">
		<div class="grade-policy-inner">
			{{#if gradepolicy.editable}}
				<div class="label-input-pair">
					<label for="exam-weight">Exam Weight:</label>
					<input type="text" name="exam-weight" value="{{gradepolicy.exam_weight}}" class="slider" size="4" />
				</div>
				<div class="label-input-pair">
					<label for="quiz-weight">Quiz Weight:</label>
					<input type="text" name="quiz-weight" value="{{gradepolicy.quiz_weight}}" class="slider" size="4" />
				</div>
				<div class="label-input-pair">
					<label for="homework-weight">Homework Weight:</label>
					<input type="text" name="homework-weight" value="{{gradepolicy.homework_weight}}" class="slider" size="4" />
				</div>
				<div class="label-input-pair">
					<label for="threshold">Passing Threshold:</label>
					<input type="text" name="threshold" value="{{gradepolicy.threshold}}" class="slider" size="4" />
				</div>
				<div class="label-input-pair">
					<label for="description">Policy Description:</label>
					<textarea name="description" cols="50" rows="2">{{gradepolicy.description}}</textarea>
				</div>
				<button type="submit">Submit</button>
				<a class="restore-defaults" href="<?php echo JRoute::_($base. '&active=progress&action=restoredefaults') ?>">Restore Defaults</a>
			{{else}}
				<p class="warning">Sorry, you do not have permission to edit the grade policy for this course</p>
			{{/if}}
		</div>
	</div>
	<div class="headers main-headers">
		<div class="cell header-student-name">
			<div class="sorter" data-sort-val="name" data-sort-dir="asc"></div>
			Name
		</div>
		<div class="header-sub">
			<div class="cell header-progress">
				Unit Progress
				<div class="details" title="This reflects what students have viewed, not the actual scores that they may have received."></div>
			</div>
			<div class="cell header-score">
				<div class="sorter" data-sort-val="score" data-sort-dir="asc"></div>
				Current Score
				<div class="details" title="{{gradepolicy.description}}"></div>
			</div>
		</div>
	</div>
	<div class="students"></div>
</script>

<script id="progress-template-row" type="text/x-handlebars-template">
	{{#each members}}
		<div class="student">
			<div class="student-clickable">
				<div class="cell student-name">
					<div class="picture-thumb">
						<img src="<?php echo Juri::base(); ?>{{this.thumb}}" />
					</div>
					<div class="name-value">
						{{this.name}}
					</div>
				</div>
				<div class="student-progress-container">
					<div class="student-progress-timeline">
						<div class="student-progress-timeline-inner length_{{countUnits ../units}}">
							{{#each ../units}}
								<div class="unit">
									<div class="unit-inner">
										<div class="unit-title">{{this.title}}</div>
										{{getFill ../../progress ../id}}
									</div>
								</div>
							{{/each}}
						</div>
					</div>
					<div class="progress-bar-container">
						<div class="progress-bar-container-inner">
							<div class="progress-bar-inner">
								{{getBar ../grades ../passing ../course_id}}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="student-details grades">
				<div class="extended-info">
					<div class="picture">
						<img src="<?php echo Juri::base(); ?>{{this.full}}" />
						<a class="more-details" href="<?php echo JRoute::_($base.'&active=progress&id=') ?>{{this.user_id}}">More details</a>
					</div>
					<div class="extended-info-extra">
						<h6>Joined Course</h6>
						<p>{{enrolled}}</p>
						<h6>Last Visit</h6>
						<p>{{lastvisit}}</p>
					</div>
				</div>
				<div class="units">
					<div class="headers">
						<div class="header-units">Unit Scores</div>
					</div>
					{{#each ../units}}
						<div class="unit-entry">
							<div class="unit-overview">
								<div class="unit-title">{{this.title}}</div>
								<div class="unit-score">
									{{getScore "units" ../../grades ../id this.id}}
								</div>
							</div>
						</div>
					{{/each}}
					<div class="unit-entry">
						<div class="unit-overview">
							<div class="unit-title">Course Average</div>
							<div class="unit-score">
								{{getScore "course" ../grades this.id ../course_id}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	{{/each}}
</script>

<script id="gradebook-template-main" type="text/x-handlebars-template">
	<div class="gradebook-container-inner">
		<div class="gradebook-column gradebook-students">
			<div class="cell search-box"><input type="text" placeholder="Search students" /></div>
			{{#each members}}
					<div class="cell cell-title cell-row{{@index}}" title="{{this.name}}" data-rownum="cell-row{{@index}}">
						{{shorten name 25}}
					</div>
				</tr>
			{{/each}}
		</div>
		<div class="slidable-outer">
			<div class="slidable">
				<div class="slidable-inner">
				</div>
			</div>
		</div>
	</div>
</script>

<script id="gradebook-template-asset" type="text/x-handlebars-template">
	{{#each assets}}
		<div class="gradebook-column" data-colnum="{{@index}}" data-asset-id="{{this.id}}">
			<div class="cell form-name" title="{{this.title}}">
				<div class="form-name-inner">
					<div class="form-title">
						{{shorten title 10}}
					</div>
					<div class="form-type">
						<select name="type">
							<option value="exam"{{ifAreEqual subtype "exam"}}>Exam</option>
							<option value="quiz"{{ifAreEqual subtype "quiz"}}>Quiz</option>
							<option value="homework"{{ifAreEqual subtype "homework"}}>Homework</option>
						</select>
					</div>
					<div class="form-delete"></div>
				</div>
			</div>
			{{#each ../members}}
				<div class="cell cell-entry cell-row{{@index}}" data-asset-id="{{../id}}" data-student-id="{{this.id}}" data-rownum="cell-row{{@index}}">
					<div class="cell-score">{{getGrade ../../grades this.id ../id}}</div>
					<div class="override{{ifIsOverride ../../grades this.id ../id}}"></div>
				</div>
			{{/each}}
		</div>
	{{/each}}
</script>

<div class="main-container">
	<div id="message-container"></div>
	<div class="loading">
		<img src="/components/com_courses/assets/img/loading-light.gif" />
	</div>

	<div class="controls-wrap">
		<div class="controls clear">
			<div title="progress view" class="progress-button button active"></div>
			<div title="gradebook view" class="gradebook-button button"></div>
			<div title="edit grade policy" class="progress_button policy button"></div>
			<div title="add a new entry" class="gradebook_button addrow button"></div>
			<div title="export to csv" class="gradebook_button export button"></div>
			<div title="refresh gradebook view" class="gradebook_button refresh button"></div>
		</div>
		<div class="fetching-rows">
			<div class="fetching-rows-inner">
				<div class="fetching-message">loading students...</div>
				<div class="fetching-rows-bar"></div>
			</div>
		</div>
	</div>

	<div class="clear"></div>

	<form action="<?php echo JRoute::_($base); ?>" class="progress-form"></form>

	<div class="clear"></div>

	<div class="navigation">
		<div class="search-box"><input type="text" placeholder="Search students" /></div>
		<div class="nav-wrap">
			<div class="prv"></div>
			<div class="nxt"></div>
			<div class="slider-container"><div class="slider"></div></div>
		</div>
	</div>
</div>