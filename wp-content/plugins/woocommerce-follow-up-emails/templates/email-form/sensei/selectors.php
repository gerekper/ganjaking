<div>
	<p class="form-field sensei sensei-courses">
		<label for="course_id"><?php esc_html_e( 'Enable for', 'follow_up_emails' ); ?></label>
		<select
			id="course_id"
			name="meta[sensei_course_id]"
			class="sensei_courses sensei-search"
			data-placeholder="<?php esc_attr_e( 'All courses&hellip;', 'follow_up_emails' ); ?>"
			data-action="fue_sensei_search_courses"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'search-courses' ) ); ?>"
			data-allow_clear="true"
		>
		<?php if ( ! empty( $course_id ) ) : ?>
			<option value="<?php echo esc_attr( $course_id ); ?>"><?php echo esc_html( get_the_title( $course_id ) ); ?></option>
		<?php endif; ?>
		</select>
	</p>

	<p class="form-field sensei sensei-lessons">
		<label for="lesson_id"><?php esc_html_e( 'Enable for', 'follow_up_emails' ); ?></label>
		<select
			id="lesson_id"
			name="meta[sensei_lesson_id]"
			class="sensei_lessons sensei-search"
			data-placeholder="<?php esc_attr_e( 'All lessons&hellip;', 'follow_up_emails' ); ?>"
			data-action="fue_sensei_search_lessons"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'search-lessons' ) ); ?>"
			data-allow_clear="true"
		>
		<?php if ( ! empty( $lesson_id ) ) : ?>
			<option value="<?php echo esc_attr( $lesson_id ); ?>"><?php echo esc_html( get_the_title( $lesson_id ) ); ?></option>
		<?php endif; ?>
		</select>
	</p>

	<p class="form-field sensei sensei-quizzes">
		<label for="quiz_id"><?php esc_html_e( 'Enable for', 'follow_up_emails' ); ?></label>
		<select
			id="quiz_id"
			name="meta[sensei_quiz_id]"
			class="sensei_quizzes sensei-search"
			data-placeholder="<?php esc_attr_e( 'All quizzes&hellip;', 'follow_up_emails' ); ?>"
			data-action="fue_sensei_search_quizzes"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'search-quizzes' ) ); ?>"
			data-allow_clear="true"
		>
		<?php if ( ! empty( $quiz_id ) ) : ?>
			<option value="<?php echo esc_attr( $quiz_id ); ?>"><?php echo esc_html( get_the_title( $quiz_id ) ); ?></option>
		<?php endif; ?>
		</select>
	</p>

	<p class="form-field sensei sensei-answers">
		<label for="question_id"><?php esc_html_e( 'Question', 'follow_up_emails' ); ?></label>
		<select
			id="question_id"
			name="meta[sensei_question_id]"
			class="sensei_questions sensei-search"
			data-placeholder="<?php esc_attr_e( 'Search for a question&hellip;', 'follow_up_emails' ); ?>"
			data-action="fue_sensei_search_questions"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'search-questions' ) ); ?>"
			data-allow_clear="true"
		>
		<?php if ( ! empty( $question_id ) ) : ?>
			<option value="<?php echo esc_attr( $question_id ); ?>"><?php echo esc_html( get_the_title( $question_id ) ); ?></option>
		<?php endif; ?>
		</select>
	</p>
	<p class="form-field sensei sensei-answers">
		<label for="answer"><?php esc_html_e( 'Answer', 'follow_up_emails' ); ?></label>
		<input type="text" class="input-text" name="meta[sensei_answer]" id="answer" value="<?php echo esc_attr( $answer ); ?>" style="width: 100%;" />
	</p>
</div>
